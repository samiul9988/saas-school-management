<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $sections = $request->filled('school_class_id')
            ? Section::where('school_class_id', $request->school_class_id)->get()
            : collect();

        $date = $request->input('date', today()->toDateString());

        $students = collect();
        if ($request->filled('school_class_id')) {
            $students = Student::where('school_class_id', $request->school_class_id)
                ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id))
                ->where('status', 'active')
                ->orderBy('roll')
                ->get();

            $existing = Attendance::whereIn('student_id', $students->pluck('id'))
                ->where('date', $date)
                ->pluck('status', 'student_id');

            $students->each(fn ($student) => $student->today_status = $existing->get($student->id));
        }

        return view('school.attendance.index', compact('classes', 'sections', 'students', 'date'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'statuses' => ['required', 'array'],
            'statuses.*' => ['in:present,absent,late,leave'],
        ]);

        foreach ($data['statuses'] as $studentId => $status) {
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $data['date']],
                [
                    'school_class_id' => $data['school_class_id'],
                    'section_id' => $data['section_id'] ?? null,
                    'status' => $status,
                ]
            );
        }

        return redirect()->route('school.attendance.index', [
            'school_class_id' => $data['school_class_id'],
            'section_id' => $data['section_id'] ?? null,
            'date' => $data['date'],
        ])->with('status', 'Attendance saved.');
    }
}
