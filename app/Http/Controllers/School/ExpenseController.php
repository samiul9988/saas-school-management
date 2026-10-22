<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Services\Accounting\AccountingService;
use App\Services\Accounting\ChartOfAccountSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::with('account', 'paymentAccount')->latest('date')->paginate(20);

        return view('school.expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        if (ChartOfAccount::count() === 0) {
            ChartOfAccountSeeder::seedFor(request()->user()->school);
        }

        $expenseAccounts = ChartOfAccount::where('type', 'expense')->where('is_active', true)->orderBy('code')->get();
        $paymentAccounts = ChartOfAccount::where('type', 'asset')->where('is_active', true)->orderBy('code')->get();

        return view('school.expenses.create', compact('expenseAccounts', 'paymentAccounts'));
    }

    public function store(Request $request, AccountingService $accounting): RedirectResponse
    {
        $data = $this->validated($request);

        $expense = Expense::create([
            'category' => $data['category'],
            'amount' => $data['amount'],
            'date' => $data['date'],
            'description' => $data['description'] ?? null,
            'method' => $data['method'],
            'reference' => $data['reference'] ?? null,
        ]);

        $expenseAccount = ChartOfAccount::findOrFail($data['account_id']);
        $paymentAccount = ChartOfAccount::findOrFail($data['payment_account_id']);
        $accounting->postExpense($expense, $paymentAccount, $expenseAccount, $request->user());

        return redirect()->route('school.expenses.index')->with('status', 'Expense recorded.');
    }

    public function destroy(Expense $expense, AccountingService $accounting): RedirectResponse
    {
        if ($expense->journal_entry_id) {
            $accounting->reverse($expense->journalEntry, request()->user());
        }

        $expense->delete();

        return redirect()->route('school.expenses.index')->with('status', 'Expense removed and its journal entry reversed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'account_id' => ['required', 'exists:chart_of_accounts,id'],
            'payment_account_id' => ['required', 'exists:chart_of_accounts,id'],
            'method' => ['required', 'in:cash,bkash,nagad,bank,other'],
            'reference' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
