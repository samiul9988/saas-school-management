<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\Income;
use App\Models\JournalEntry;
use App\Models\Payroll;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AccountingService
{
    /**
     * @param  array<int, array{account_id: int, debit?: float, credit?: float, note?: ?string}>  $lines
     */
    public function post(School $school, string $date, array $lines, ?string $reference = null, ?string $description = null, ?string $sourceType = null, ?int $sourceId = null, ?User $createdBy = null): JournalEntry
    {
        $totalDebit = round(array_sum(array_column($lines, 'debit')), 2);
        $totalCredit = round(array_sum(array_column($lines, 'credit')), 2);

        if (count($lines) < 2) {
            throw new RuntimeException('A journal entry requires at least two lines.');
        }

        if ($totalDebit !== $totalCredit) {
            throw new RuntimeException("Journal entry is not balanced: debit {$totalDebit} != credit {$totalCredit}.");
        }

        return DB::transaction(function () use ($school, $date, $lines, $reference, $description, $sourceType, $sourceId, $createdBy) {
            $entry = JournalEntry::create([
                'school_id' => $school->id,
                'date' => $date,
                'reference' => $reference,
                'description' => $description,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'status' => 'posted',
                'created_by' => $createdBy?->id,
            ]);

            foreach ($lines as $line) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'note' => $line['note'] ?? null,
                ]);
            }

            return $entry;
        });
    }

    public function reverse(JournalEntry $entry, ?User $createdBy = null): JournalEntry
    {
        if ($entry->status === 'reversed') {
            throw new RuntimeException('This journal entry has already been reversed.');
        }

        $reversal = DB::transaction(function () use ($entry, $createdBy) {
            $lines = $entry->lines->map(fn ($line) => [
                'account_id' => $line->account_id,
                'debit' => (float) $line->credit,
                'credit' => (float) $line->debit,
                'note' => 'Reversal of entry #'.$entry->id,
            ])->all();

            $reversal = $this->post(
                $entry->school,
                now()->toDateString(),
                $lines,
                $entry->reference,
                'Reversal of: '.$entry->description,
                $entry->source_type,
                $entry->source_id,
                $createdBy,
            );

            $reversal->update(['reversal_of_id' => $entry->id]);
            $entry->update(['status' => 'reversed']);

            return $reversal;
        });

        return $reversal;
    }

    public function postFeePayment(FeePayment $payment, ChartOfAccount $paymentAccount, ?User $createdBy = null): void
    {
        if ($payment->journal_entry_id) {
            return;
        }

        $school = $payment->school;
        $incomeAccount = ChartOfAccount::withoutGlobalScope('school')
            ->where('school_id', $school->id)
            ->where('code', ChartOfAccountSeeder::STUDENT_FEE_INCOME)
            ->firstOrFail();

        DB::transaction(function () use ($payment, $paymentAccount, $incomeAccount, $school, $createdBy) {
            $entry = $this->post(
                $school,
                $payment->paid_on->toDateString(),
                [
                    ['account_id' => $paymentAccount->id, 'debit' => (float) $payment->amount, 'note' => 'Fee payment received'],
                    ['account_id' => $incomeAccount->id, 'credit' => (float) $payment->amount, 'note' => 'Student fee income'],
                ],
                $payment->receipt_number,
                'Fee payment for receipt '.$payment->receipt_number,
                FeePayment::class,
                $payment->id,
                $createdBy,
            );

            $payment->update(['payment_account_id' => $paymentAccount->id, 'journal_entry_id' => $entry->id]);
        });
    }

    public function postIncome(Income $income, ChartOfAccount $paymentAccount, ChartOfAccount $incomeAccount, ?User $createdBy = null): void
    {
        if ($income->journal_entry_id) {
            return;
        }

        $school = $income->school;

        DB::transaction(function () use ($income, $paymentAccount, $incomeAccount, $school, $createdBy) {
            $entry = $this->post(
                $school,
                $income->date->toDateString(),
                [
                    ['account_id' => $paymentAccount->id, 'debit' => (float) $income->amount, 'note' => $income->category],
                    ['account_id' => $incomeAccount->id, 'credit' => (float) $income->amount, 'note' => $income->category],
                ],
                $income->reference,
                $income->description ?: $income->category,
                Income::class,
                $income->id,
                $createdBy,
            );

            $income->update([
                'account_id' => $incomeAccount->id,
                'payment_account_id' => $paymentAccount->id,
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function postExpense(Expense $expense, ChartOfAccount $paymentAccount, ChartOfAccount $expenseAccount, ?User $createdBy = null): void
    {
        if ($expense->journal_entry_id) {
            return;
        }

        $school = $expense->school;

        DB::transaction(function () use ($expense, $paymentAccount, $expenseAccount, $school, $createdBy) {
            $entry = $this->post(
                $school,
                $expense->date->toDateString(),
                [
                    ['account_id' => $expenseAccount->id, 'debit' => (float) $expense->amount, 'note' => $expense->category],
                    ['account_id' => $paymentAccount->id, 'credit' => (float) $expense->amount, 'note' => $expense->category],
                ],
                $expense->reference,
                $expense->description ?: $expense->category,
                Expense::class,
                $expense->id,
                $createdBy,
            );

            $expense->update([
                'account_id' => $expenseAccount->id,
                'payment_account_id' => $paymentAccount->id,
                'journal_entry_id' => $entry->id,
            ]);
        });
    }

    public function postPayroll(Payroll $payroll, ChartOfAccount $paymentAccount, ?User $createdBy = null): void
    {
        if ($payroll->journal_entry_id) {
            return;
        }

        $school = $payroll->school;
        $salaryExpenseAccount = ChartOfAccount::withoutGlobalScope('school')
            ->where('school_id', $school->id)
            ->where('code', ChartOfAccountSeeder::SALARY_EXPENSE)
            ->firstOrFail();

        DB::transaction(function () use ($payroll, $paymentAccount, $salaryExpenseAccount, $school, $createdBy) {
            $entry = $this->post(
                $school,
                now()->toDateString(),
                [
                    ['account_id' => $salaryExpenseAccount->id, 'debit' => (float) $payroll->net_salary, 'note' => 'Salary payment'],
                    ['account_id' => $paymentAccount->id, 'credit' => (float) $payroll->net_salary, 'note' => 'Salary payment'],
                ],
                null,
                'Payroll '.$payroll->month.'/'.$payroll->year.' for teacher #'.$payroll->teacher_id,
                Payroll::class,
                $payroll->id,
                $createdBy,
            );

            $payroll->update(['payment_account_id' => $paymentAccount->id, 'journal_entry_id' => $entry->id]);
        });
    }
}
