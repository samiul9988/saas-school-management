<?php

namespace Tests\Feature\Accounting;

use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Income;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Teacher;
use App\Models\User;
use App\Services\Accounting\AccountingService;
use App\Services\Accounting\ChartOfAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AccountingServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeSchoolAdmin(): User
    {
        $school = School::create([
            'name' => 'Accounting Test School',
            'slug' => Str::slug('Accounting Test School').'-'.Str::random(5),
            'email' => fake()->unique()->safeEmail(),
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'School Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school->id,
        ]);

        ChartOfAccountSeeder::seedFor($school);

        return $admin;
    }

    public function test_default_chart_of_accounts_is_seeded_idempotently(): void
    {
        $admin = $this->makeSchoolAdmin();

        $countBefore = ChartOfAccount::count();
        ChartOfAccountSeeder::seedFor($admin->school);
        $countAfter = ChartOfAccount::count();

        $this->assertSame($countBefore, $countAfter, 'Re-seeding must not create duplicate accounts.');
        $this->assertGreaterThan(0, $countAfter);
    }

    public function test_posting_fee_payment_creates_balanced_journal_entry(): void
    {
        $admin = $this->makeSchoolAdmin();
        $cash = ChartOfAccount::where('code', ChartOfAccountSeeder::CASH)->first();

        $student = Student::create(['school_id' => $admin->school_id, 'name' => 'Student A', 'status' => 'active']);
        $feeType = FeeType::create(['school_id' => $admin->school_id, 'name' => 'Tuition']);
        $studentFee = StudentFee::create(['school_id' => $admin->school_id, 'student_id' => $student->id, 'fee_type_id' => $feeType->id, 'amount' => 1000, 'paid_amount' => 0, 'status' => 'unpaid']);

        $payment = FeePayment::create([
            'school_id' => $admin->school_id,
            'student_fee_id' => $studentFee->id,
            'student_id' => $student->id,
            'receipt_number' => FeePayment::generateReceiptNumber($admin->school_id),
            'amount' => 500,
            'method' => 'cash',
            'paid_on' => now(),
            'received_by' => $admin->id,
        ]);

        app(AccountingService::class)->postFeePayment($payment, $cash, $admin);

        $payment->refresh();
        $this->assertNotNull($payment->journal_entry_id);

        $entry = $payment->journalEntry;
        $this->assertSame(500.0, (float) $entry->lines->sum('debit'));
        $this->assertSame(500.0, (float) $entry->lines->sum('credit'));
    }

    public function test_posting_fee_payment_twice_does_not_duplicate_journal_entry(): void
    {
        $admin = $this->makeSchoolAdmin();
        $cash = ChartOfAccount::where('code', ChartOfAccountSeeder::CASH)->first();

        $student = Student::create(['school_id' => $admin->school_id, 'name' => 'Student A', 'status' => 'active']);
        $feeType = FeeType::create(['school_id' => $admin->school_id, 'name' => 'Tuition']);
        $studentFee = StudentFee::create(['school_id' => $admin->school_id, 'student_id' => $student->id, 'fee_type_id' => $feeType->id, 'amount' => 1000, 'paid_amount' => 0, 'status' => 'unpaid']);

        $payment = FeePayment::create([
            'school_id' => $admin->school_id,
            'student_fee_id' => $studentFee->id,
            'student_id' => $student->id,
            'receipt_number' => FeePayment::generateReceiptNumber($admin->school_id),
            'amount' => 500,
            'method' => 'cash',
            'paid_on' => now(),
            'received_by' => $admin->id,
        ]);

        $service = app(AccountingService::class);
        $service->postFeePayment($payment, $cash, $admin);
        $firstEntryId = $payment->fresh()->journal_entry_id;

        $service->postFeePayment($payment->fresh(), $cash, $admin);

        $this->assertSame(1, JournalEntry::count());
        $this->assertSame($firstEntryId, $payment->fresh()->journal_entry_id);
    }

    public function test_income_and_expense_post_balanced_entries_via_http(): void
    {
        $admin = $this->makeSchoolAdmin();
        $cash = ChartOfAccount::where('code', ChartOfAccountSeeder::CASH)->first();
        $incomeAccount = ChartOfAccount::where('type', 'income')->first();
        $expenseAccount = ChartOfAccount::where('type', 'expense')->first();

        $this->actingAs($admin)->post(route('school.incomes.store'), [
            'category' => 'Donation',
            'account_id' => $incomeAccount->id,
            'payment_account_id' => $cash->id,
            'method' => 'cash',
            'amount' => 300,
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($admin)->post(route('school.expenses.store'), [
            'category' => 'Utility',
            'account_id' => $expenseAccount->id,
            'payment_account_id' => $cash->id,
            'method' => 'cash',
            'amount' => 120,
            'date' => now()->toDateString(),
        ]);

        $income = Income::first();
        $expense = Expense::first();

        $this->assertNotNull($income->journal_entry_id);
        $this->assertNotNull($expense->journal_entry_id);
        $this->assertSame(300.0, (float) $income->journalEntry->lines->sum('debit'));
        $this->assertSame(120.0, (float) $expense->journalEntry->lines->sum('debit'));
    }

    public function test_deleting_income_reverses_journal_entry_instead_of_silently_removing_it(): void
    {
        $admin = $this->makeSchoolAdmin();
        $cash = ChartOfAccount::where('code', ChartOfAccountSeeder::CASH)->first();
        $incomeAccount = ChartOfAccount::where('type', 'income')->first();

        $this->actingAs($admin)->post(route('school.incomes.store'), [
            'category' => 'Donation',
            'account_id' => $incomeAccount->id,
            'payment_account_id' => $cash->id,
            'method' => 'cash',
            'amount' => 300,
            'date' => now()->toDateString(),
        ]);

        $income = Income::first();
        $originalEntryId = $income->journal_entry_id;

        $this->actingAs($admin)->delete(route('school.incomes.destroy', $income));

        $this->assertDatabaseMissing('incomes', ['id' => $income->id]);
        $this->assertDatabaseHas('journal_entries', ['id' => $originalEntryId, 'status' => 'reversed']);
        $this->assertSame(2, JournalEntry::count(), 'Original entry must remain, plus one reversal entry.');
        $this->assertSame(0.0, (float) JournalEntryLine::where('account_id', $cash->id)->sum('debit') - (float) JournalEntryLine::where('account_id', $cash->id)->sum('credit'));
    }

    public function test_payroll_mark_paid_posts_balanced_salary_journal_entry(): void
    {
        $admin = $this->makeSchoolAdmin();

        $teacher = Teacher::create(['school_id' => $admin->school_id, 'name' => 'Teacher A', 'status' => 'active']);
        SalaryStructure::create(['school_id' => $admin->school_id, 'teacher_id' => $teacher->id, 'basic_salary' => 20000, 'allowances' => 2000, 'deductions' => 500]);

        $this->actingAs($admin)->post(route('school.payrolls.generate'), ['month' => now()->month, 'year' => now()->year]);
        $payroll = Payroll::first();

        $this->actingAs($admin)->post(route('school.payrolls.mark-paid', $payroll), ['method' => 'bank']);

        $payroll->refresh();
        $this->assertSame('paid', $payroll->status);
        $this->assertNotNull($payroll->journal_entry_id);
        $this->assertSame((float) $payroll->net_salary, (float) $payroll->journalEntry->lines->sum('debit'));
        $this->assertSame((float) $payroll->net_salary, (float) $payroll->journalEntry->lines->sum('credit'));
    }

    public function test_trial_balance_is_always_balanced_across_multiple_transaction_types(): void
    {
        $admin = $this->makeSchoolAdmin();
        $cash = ChartOfAccount::where('code', ChartOfAccountSeeder::CASH)->first();
        $incomeAccount = ChartOfAccount::where('type', 'income')->first();
        $expenseAccount = ChartOfAccount::where('type', 'expense')->first();

        $this->actingAs($admin)->post(route('school.incomes.store'), [
            'category' => 'Donation', 'account_id' => $incomeAccount->id, 'payment_account_id' => $cash->id,
            'method' => 'cash', 'amount' => 500, 'date' => now()->toDateString(),
        ]);
        $this->actingAs($admin)->post(route('school.expenses.store'), [
            'category' => 'Utility', 'account_id' => $expenseAccount->id, 'payment_account_id' => $cash->id,
            'method' => 'cash', 'amount' => 200, 'date' => now()->toDateString(),
        ]);

        $totalDebit = (float) JournalEntryLine::sum('debit');
        $totalCredit = (float) JournalEntryLine::sum('credit');

        $this->assertSame($totalDebit, $totalCredit);
    }

    public function test_tenant_isolation_for_chart_of_accounts(): void
    {
        $adminA = $this->makeSchoolAdmin();
        $adminB = $this->makeSchoolAdmin();

        $this->actingAs($adminA);
        $visible = ChartOfAccount::count();

        $this->assertGreaterThan(0, $visible);
        $this->assertSame($visible, ChartOfAccount::where('school_id', $adminA->school_id)->count());
    }

    public function test_accounting_report_pages_render(): void
    {
        $admin = $this->makeSchoolAdmin();

        $this->actingAs($admin)->get(route('school.accounts.index'))->assertOk();
        $this->actingAs($admin)->get(route('school.journal-entries.index'))->assertOk();
        $this->actingAs($admin)->get(route('school.journal-entries.create'))->assertOk();
        $this->actingAs($admin)->get(route('school.accounting-reports.income-expense'))->assertOk();
        $this->actingAs($admin)->get(route('school.accounting-reports.cash-book'))->assertOk();
        $this->actingAs($admin)->get(route('school.accounting-reports.bank-book'))->assertOk();
        $this->actingAs($admin)->get(route('school.accounting-reports.ledger'))->assertOk();
        $this->actingAs($admin)->get(route('school.accounting-reports.trial-balance'))->assertOk();
        $this->actingAs($admin)->get(route('school.accounting-reports.profit-loss'))->assertOk();
        $this->actingAs($admin)->get(route('school.accounting-reports.balance-sheet'))->assertOk();
        $this->actingAs($admin)->get(route('school.accounting-reports.fee-collection'))->assertOk();
    }

    public function test_manual_journal_entry_must_balance(): void
    {
        $admin = $this->makeSchoolAdmin();
        $cash = ChartOfAccount::where('code', ChartOfAccountSeeder::CASH)->first();
        $incomeAccount = ChartOfAccount::where('type', 'income')->first();

        $response = $this->actingAs($admin)->post(route('school.journal-entries.store'), [
            'date' => now()->toDateString(),
            'description' => 'Unbalanced test entry',
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0],
                ['account_id' => $incomeAccount->id, 'debit' => 0, 'credit' => 50],
            ],
        ]);

        $response->assertRedirect();
        $this->assertSame(0, JournalEntry::count(), 'Unbalanced entries must be rejected.');
    }
}
