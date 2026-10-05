<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = Student::with('schoolClass', 'section')
            ->when($request->filled('school_class_id'), fn ($q) => $q->where('school_class_id', $request->school_class_id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $classes = SchoolClass::orderBy('sort_order')->get();

        return view('school.students.index', compact('students', 'classes'));
    }

    public function create(): View
    {
        $classes = SchoolClass::with('sections')->orderBy('sort_order')->get();
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();

        return view('school.students.create', compact('classes', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        Student::create($this->validated($request));

        return redirect()->route('school.students.index')->with('status', 'Student admitted.');
    }

    public function show(Student $student): View
    {
        $student->load('schoolClass', 'section', 'academicYear', 'attendances');

        return view('school.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $classes = SchoolClass::with('sections')->orderBy('sort_order')->get();
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();

        return view('school.students.edit', compact('student', 'classes', 'academicYears'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->update($this->validated($request));

        return redirect()->route('school.students.index')->with('status', 'Student updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('school.students.index')->with('status', 'Student removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'admission_number' => ['nullable', 'string', 'max:255'],
            'roll' => ['nullable', 'string', 'max:50'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,transferred,graduated'],
        ]);
    }
}
