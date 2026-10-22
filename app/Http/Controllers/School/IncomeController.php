<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Income;
use App\Services\Accounting\AccountingService;
use App\Services\Accounting\ChartOfAccountSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(): View
    {
        $incomes = Income::with('account', 'paymentAccount')->latest('date')->paginate(20);

        return view('school.incomes.index', compact('incomes'));
    }

    public function create(): View
    {
        if (ChartOfAccount::count() === 0) {
            ChartOfAccountSeeder::seedFor(request()->user()->school);
        }

        $incomeAccounts = ChartOfAccount::where('type', 'income')->where('is_active', true)->orderBy('code')->get();
        $paymentAccounts = ChartOfAccount::where('type', 'asset')->where('is_active', true)->orderBy('code')->get();

        return view('school.incomes.create', compact('incomeAccounts', 'paymentAccounts'));
    }

    public function store(Request $request, AccountingService $accounting): RedirectResponse
    {
        $data = $this->validated($request);

        $income = Income::create([
            'category' => $data['category'],
            'amount' => $data['amount'],
            'date' => $data['date'],
            'description' => $data['description'] ?? null,
            'method' => $data['method'],
            'reference' => $data['reference'] ?? null,
        ]);

        $incomeAccount = ChartOfAccount::findOrFail($data['account_id']);
        $paymentAccount = ChartOfAccount::findOrFail($data['payment_account_id']);
        $accounting->postIncome($income, $paymentAccount, $incomeAccount, $request->user());

        return redirect()->route('school.incomes.index')->with('status', 'Income recorded.');
    }

    public function destroy(Income $income, AccountingService $accounting): RedirectResponse
    {
        if ($income->journal_entry_id) {
            $accounting->reverse($income->journalEntry, request()->user());
        }

        $income->delete();

        return redirect()->route('school.incomes.index')->with('status', 'Income removed and its journal entry reversed.');
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
