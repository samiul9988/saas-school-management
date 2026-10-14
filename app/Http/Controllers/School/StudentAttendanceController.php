<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Period;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentAttendanceController extends Controller
{
    private function sections()
    {
        return Section::with('schoolClass', 'shift')->orderBy('serial')->orderBy('name')->get();
    }

    private function periods()
    {
        return Period::orderBy('serial')->get();
    }

    public function input(Request $request): View
    {
        $sections = $this->sections();
        $periods = $this->periods();
        $date = $request->get('date', now()->toDateString());
        $section = null;
        $students = collect();

        if ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $students = Student::where('section_id', $section->id)->where('status', 'active')->orderBy('roll')->get();
        }

        return view('school.attendance.input', compact('sections', 'periods', 'date', 'section', 'students'));
    }

    public function inputStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'section_id' => ['required', 'exists:sections,id'],
            'period_id' => ['nullable', 'exists:periods,id'],
            'absent_ids' => ['array'],
            'absent_ids.*' => ['exists:students,id'],
        ]);

        $section = Section::findOrFail($data['section_id']);
        $absentIds = $data['absent_ids'] ?? [];
        $students = Student::where('section_id', $section->id)->where('status', 'active')->get();

        foreach ($students as $student) {
            Attendance::updateOrCreate(
                ['student_id' => $student->id, 'date' => $data['date'], 'period_id' => $data['period_id'] ?? null],
                [
                    'school_class_id' => $section->school_class_id,
                    'section_id' => $section->id,
                    'status' => in_array($student->id, $absentIds) ? 'absent' : 'present',
                ]
            );
        }

        return redirect()->route('school.student-attendance.input', [
            'section_id' => $section->id, 'date' => $data['date'], 'period_id' => $data['period_id'] ?? null,
        ])->with('status', 'Attendance saved.');
    }

    public function update(Request $request): View
    {
        $sections = $this->sections();
        $periods = $this->periods();
        $date = $request->get('date', now()->toDateString());
        $section = null;
        $students = collect();

        if ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $students = Student::where('section_id', $section->id)->where('status', 'active')->orderBy('roll')->get();

            $existing = Attendance::where('date', $date)
                ->where('period_id', $request->get('period_id') ?: null)
                ->whereIn('student_id', $students->pluck('id'))
                ->pluck('status', 'student_id');

            $students->each(fn ($student) => $student->current_status = $existing->get($student->id));
        }

        return view('school.attendance.update', compact('sections', 'periods', 'date', 'section', 'students'));
    }

    public function updateStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'section_id' => ['required', 'exists:sections,id'],
            'period_id' => ['nullable', 'exists:periods,id'],
            'absent_ids' => ['array'],
            'absent_ids.*' => ['exists:students,id'],
        ]);

        $section = Section::findOrFail($data['section_id']);
        $absentIds = $data['absent_ids'] ?? [];
        $students = Student::where('section_id', $section->id)->where('status', 'active')->get();

        foreach ($students as $student) {
            Attendance::updateOrCreate(
                ['student_id' => $student->id, 'date' => $data['date'], 'period_id' => $data['period_id'] ?? null],
                [
                    'school_class_id' => $section->school_class_id,
                    'section_id' => $section->id,
                    'status' => in_array($student->id, $absentIds) ? 'absent' : 'present',
                ]
            );
        }

        return redirect()->route('school.student-attendance.update', [
            'section_id' => $section->id, 'date' => $data['date'], 'period_id' => $data['period_id'] ?? null,
        ])->with('status', 'Attendance updated.');
    }

    public function leave(Request $request): View
    {
        $student = null;
        $attendances = collect();

        if ($request->filled('student_id')) {
            $student = Student::where('id', $request->student_id)
                ->orWhere('admission_number', $request->student_id)
                ->first();

            if ($student) {
                $attendances = Attendance::where('student_id', $student->id)
                    ->where('status', 'leave')
                    ->orderByDesc('date')
                    ->get();
            }
        }

        return view('school.attendance.leave', compact('student', 'attendances'));
    }

    public function leaveStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        $student = Student::findOrFail($data['student_id']);
        $period = \Carbon\CarbonPeriod::create($data['from_date'], $data['to_date']);

        foreach ($period as $date) {
            Attendance::updateOrCreate(
                ['student_id' => $student->id, 'date' => $date->toDateString(), 'period_id' => null],
                [
                    'school_class_id' => $student->school_class_id,
                    'section_id' => $student->section_id,
                    'status' => 'leave',
                ]
            );
        }

        return redirect()->route('school.student-attendance.leave', ['student_id' => $student->id])
            ->with('status', 'Leave recorded.');
    }
}
