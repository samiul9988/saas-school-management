<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\FeePayment;
use App\Models\JournalEntryLine;
use App\Models\StudentFee;
use App\Services\Accounting\ChartOfAccountSeeder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingReportController extends Controller
{
    private function dateRange(Request $request): array
    {
        return [
            $request->date('from') ?? now()->startOfMonth(),
            $request->date('to') ?? now(),
        ];
    }

    public function incomeExpense(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);

        $lines = JournalEntryLine::whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$from, $to])->where('status', 'posted'))
            ->with('account')
            ->get();

        $income = $lines->filter(fn ($l) => $l->account->type === 'income')->groupBy('account.name')
            ->map(fn ($group) => $group->sum('credit') - $group->sum('debit'));

        $expense = $lines->filter(fn ($l) => $l->account->type === 'expense')->groupBy('account.name')
            ->map(fn ($group) => $group->sum('debit') - $group->sum('credit'));

        return view('school.accounting-reports.income-expense', [
            'from' => $from, 'to' => $to,
            'income' => $income, 'expense' => $expense,
            'totalIncome' => $income->sum(), 'totalExpense' => $expense->sum(),
        ]);
    }

    public function cashBook(Request $request): View
    {
        return $this->accountBook($request, ChartOfAccountSeeder::CASH, 'school.accounting-reports.cash-book');
    }

    public function bankBook(Request $request): View
    {
        return $this->accountBook($request, ChartOfAccountSeeder::BANK, 'school.accounting-reports.bank-book');
    }

    private function accountBook(Request $request, string $code, string $view): View
    {
        [$from, $to] = $this->dateRange($request);

        $account = ChartOfAccount::where('code', $code)->first();

        $lines = collect();
        $openingBalance = 0;

        if ($account) {
            $openingBalance = (float) JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($q) => $q->where('date', '<', $from)->where('status', 'posted'))
                ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) as balance')
                ->value('balance');

            $lines = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$from, $to])->where('status', 'posted'))
                ->with('journalEntry')
                ->get()
                ->sortBy(fn ($l) => $l->journalEntry->date);
        }

        $running = $openingBalance;
        $rows = $lines->map(function ($line) use (&$running) {
            $running += (float) $line->debit - (float) $line->credit;

            return [
                'date' => $line->journalEntry->date,
                'description' => $line->journalEntry->description,
                'debit' => $line->debit,
                'credit' => $line->credit,
                'balance' => $running,
            ];
        });

        return view($view, [
            'from' => $from, 'to' => $to, 'account' => $account,
            'openingBalance' => $openingBalance, 'rows' => $rows, 'closingBalance' => $running,
        ]);
    }

    public function ledger(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);

        $accounts = ChartOfAccount::orderBy('code')->get();
        $account = null;
        $rows = collect();
        $openingBalance = 0;
        $closingBalance = 0;

        if ($request->filled('account_id')) {
            $account = ChartOfAccount::findOrFail($request->integer('account_id'));

            $openingBalance = (float) JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($q) => $q->where('date', '<', $from)->where('status', 'posted'))
                ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) as balance')
                ->value('balance');

            $lines = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$from, $to])->where('status', 'posted'))
                ->with('journalEntry')
                ->get()
                ->sortBy(fn ($l) => $l->journalEntry->date);

            $running = $openingBalance;
            $rows = $lines->map(function ($line) use (&$running) {
                $running += (float) $line->debit - (float) $line->credit;

                return [
                    'date' => $line->journalEntry->date,
                    'description' => $line->journalEntry->description,
                    'debit' => $line->debit,
                    'credit' => $line->credit,
                    'balance' => $running,
                ];
            });
            $closingBalance = $running;
        }

        return view('school.accounting-reports.ledger', compact('accounts', 'account', 'from', 'to', 'rows', 'openingBalance', 'closingBalance'));
    }

    public function trialBalance(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);

        $accounts = ChartOfAccount::orderBy('code')->get();

        $rows = $accounts->map(function ($account) use ($from, $to) {
            $sums = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$from, $to])->where('status', 'posted'))
                ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
                ->first();

            return [
                'account' => $account,
                'debit' => (float) $sums->debit,
                'credit' => (float) $sums->credit,
            ];
        })->filter(fn ($r) => $r['debit'] != 0 || $r['credit'] != 0)->values();

        return view('school.accounting-reports.trial-balance', [
            'from' => $from, 'to' => $to, 'rows' => $rows,
            'totalDebit' => $rows->sum('debit'), 'totalCredit' => $rows->sum('credit'),
        ]);
    }

    public function profitAndLoss(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);

        $lines = JournalEntryLine::whereHas('journalEntry', fn ($q) => $q->whereBetween('date', [$from, $to])->where('status', 'posted'))
            ->with('account')
            ->get();

        $income = $lines->filter(fn ($l) => $l->account->type === 'income')->sum(fn ($l) => $l->credit - $l->debit);
        $expense = $lines->filter(fn ($l) => $l->account->type === 'expense')->sum(fn ($l) => $l->debit - $l->credit);

        return view('school.accounting-reports.profit-loss', [
            'from' => $from, 'to' => $to,
            'totalIncome' => $income, 'totalExpense' => $expense, 'netProfit' => $income - $expense,
        ]);
    }

    public function balanceSheet(Request $request): View
    {
        $asOf = $request->date('as_of') ?? now();

        $accounts = ChartOfAccount::whereIn('type', ['asset', 'liability', 'equity'])->orderBy('code')->get();

        $balances = $accounts->map(function ($account) use ($asOf) {
            $sums = JournalEntryLine::where('account_id', $account->id)
                ->whereHas('journalEntry', fn ($q) => $q->where('date', '<=', $asOf)->where('status', 'posted'))
                ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(credit),0) as credit')
                ->first();

            $balance = $account->type === 'asset'
                ? (float) $sums->debit - (float) $sums->credit
                : (float) $sums->credit - (float) $sums->debit;

            return ['account' => $account, 'balance' => $balance];
        })->filter(fn ($r) => $r['balance'] != 0)->values();

        // Retained profit/loss (income - expense to date) rolled into equity.
        $plLines = JournalEntryLine::whereHas('journalEntry', fn ($q) => $q->where('date', '<=', $asOf)->where('status', 'posted'))
            ->with('account')
            ->get();
        $retainedEarnings = $plLines->filter(fn ($l) => $l->account->type === 'income')->sum(fn ($l) => $l->credit - $l->debit)
            - $plLines->filter(fn ($l) => $l->account->type === 'expense')->sum(fn ($l) => $l->debit - $l->credit);

        $assets = $balances->filter(fn ($r) => $r['account']->type === 'asset');
        $liabilities = $balances->filter(fn ($r) => $r['account']->type === 'liability');
        $equity = $balances->filter(fn ($r) => $r['account']->type === 'equity');

        return view('school.accounting-reports.balance-sheet', [
            'asOf' => $asOf, 'assets' => $assets, 'liabilities' => $liabilities, 'equity' => $equity,
            'retainedEarnings' => $retainedEarnings,
            'totalAssets' => $assets->sum('balance'),
            'totalLiabilitiesAndEquity' => $liabilities->sum('balance') + $equity->sum('balance') + $retainedEarnings,
        ]);
    }

    public function feeCollection(Request $request): View
    {
        [$from, $to] = $this->dateRange($request);

        $collected = FeePayment::whereBetween('paid_on', [$from, $to])->sum('amount');
        $outstanding = StudentFee::whereIn('status', ['unpaid', 'partial'])->get()->sum(fn ($fee) => $fee->dueAmount());

        $byMethod = FeePayment::whereBetween('paid_on', [$from, $to])
            ->selectRaw('method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('method')
            ->get();

        return view('school.accounting-reports.fee-collection', compact('from', 'to', 'collected', 'outstanding', 'byMethod'));
    }
}
