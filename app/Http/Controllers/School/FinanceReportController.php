<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\Income;
use App\Models\StudentFee;
use Illuminate\View\View;

class FinanceReportController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_due' => StudentFee::where('status', '!=', 'paid')->get()->sum(fn ($fee) => $fee->dueAmount()),
            'collected_this_month' => FeePayment::whereMonth('paid_on', now()->month)->whereYear('paid_on', now()->year)->sum('amount'),
            'income_this_month' => Income::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount'),
            'expense_this_month' => Expense::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount'),
        ];

        $recentPayments = FeePayment::with('student')->latest('paid_on')->limit(10)->get();

        return view('school.finance.index', compact('stats', 'recentPayments'));
    }
}
