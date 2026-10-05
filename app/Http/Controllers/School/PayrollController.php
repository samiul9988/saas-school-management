<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $payrolls = Payroll::with('teacher')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        return view('school.payroll.payrolls.index', compact('payrolls', 'month', 'year'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000'],
        ]);

        $structures = SalaryStructure::all();
        $count = 0;

        foreach ($structures as $structure) {
            $exists = Payroll::where('teacher_id', $structure->teacher_id)
                ->where('month', $data['month'])
                ->where('year', $data['year'])
                ->exists();

            if ($exists) {
                continue;
            }

            Payroll::create([
                'teacher_id' => $structure->teacher_id,
                'month' => $data['month'],
                'year' => $data['year'],
                'basic_salary' => $structure->basic_salary,
                'allowances' => $structure->allowances,
                'deductions' => $structure->deductions,
                'net_salary' => $structure->netSalary(),
                'status' => 'pending',
            ]);
            $count++;
        }

        return redirect()->route('school.payrolls.index', $data)->with('status', "Generated payroll for {$count} teacher(s).");
    }

    public function markPaid(Payroll $payroll): RedirectResponse
    {
        $payroll->update(['status' => 'paid', 'paid_on' => now()]);

        return redirect()->back()->with('status', 'Marked as paid.');
    }
}
