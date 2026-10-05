<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(): View
    {
        $exams = Exam::with('academicYear')->latest('starts_on')->get();

        return view('school.exams.index', compact('exams'));
    }

    public function create(): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();

        return view('school.exams.create', compact('academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        Exam::create($this->validated($request));

        return redirect()->route('school.exams.index')->with('status', 'Exam created.');
    }

    public function show(Exam $exam): View
    {
        $exam->load('examSubjects.subject', 'examSubjects.schoolClass');

        return view('school.exams.show', compact('exam'));
    }

    public function edit(Exam $exam): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();

        return view('school.exams.edit', compact('exam', 'academicYears'));
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $exam->update($this->validated($request));

        return redirect()->route('school.exams.index')->with('status', 'Exam updated.');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();

        return redirect()->route('school.exams.index')->with('status', 'Exam deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'status' => ['required', 'in:upcoming,ongoing,completed'],
        ]);
    }
}
