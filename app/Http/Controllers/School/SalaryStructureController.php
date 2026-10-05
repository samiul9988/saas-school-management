<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SalaryStructure;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryStructureController extends Controller
{
    public function index(): View
    {
        $structures = SalaryStructure::with('teacher')->get();
        $teachersWithout = Teacher::whereDoesntHave('salaryStructure')->where('status', 'active')->get();

        return view('school.payroll.structures.index', compact('structures', 'teachersWithout'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowances' => ['required', 'numeric', 'min:0'],
            'deductions' => ['required', 'numeric', 'min:0'],
        ]);

        SalaryStructure::updateOrCreate(['teacher_id' => $data['teacher_id']], $data);

        return redirect()->route('school.salary-structures.index')->with('status', 'Salary structure saved.');
    }

    public function destroy(SalaryStructure $salaryStructure): RedirectResponse
    {
        $salaryStructure->delete();

        return redirect()->route('school.salary-structures.index')->with('status', 'Salary structure removed.');
    }
}
