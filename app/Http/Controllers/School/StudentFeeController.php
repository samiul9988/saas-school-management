<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeType;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentFeeController extends Controller
{
    public function index(Request $request): View
    {
        $fees = StudentFee::with('student', 'feeType')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('school.student-fees.index', compact('fees'));
    }

    public function create(): View
    {
        $feeTypes = FeeType::orderBy('name')->get();
        $classes = SchoolClass::orderBy('sort_order')->get();
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();

        return view('school.student-fees.create', compact('feeTypes', 'classes', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fee_type_id' => ['required', 'exists:fee_types,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ]);

        $students = Student::where('school_class_id', $data['school_class_id'])->where('status', 'active')->get();

        foreach ($students as $student) {
            StudentFee::create([
                'student_id' => $student->id,
                'fee_type_id' => $data['fee_type_id'],
                'academic_year_id' => $data['academic_year_id'] ?? null,
                'amount' => $data['amount'],
                'due_date' => $data['due_date'] ?? null,
            ]);
        }

        return redirect()->route('school.student-fees.index')
            ->with('status', "Fee assigned to {$students->count()} student(s).");
    }

    public function destroy(StudentFee $studentFee): RedirectResponse
    {
        $studentFee->delete();

        return redirect()->route('school.student-fees.index')->with('status', 'Fee assignment removed.');
    }
}
