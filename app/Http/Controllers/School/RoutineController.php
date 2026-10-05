<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\RoutinePeriod;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoutineController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $periods = collect();

        if ($request->filled('school_class_id')) {
            $periods = RoutinePeriod::where('school_class_id', $request->school_class_id)
                ->when($request->filled('section_id'), fn ($q) => $q->where('section_id', $request->section_id))
                ->with('subject', 'teacher')
                ->orderBy('day_of_week')
                ->orderBy('starts_at')
                ->get()
                ->groupBy('day_of_week');
        }

        $sections = $request->filled('school_class_id')
            ? Section::where('school_class_id', $request->school_class_id)->get()
            : collect();

        return view('school.routine.index', compact('classes', 'sections', 'periods'));
    }

    public function create(): View
    {
        $classes = SchoolClass::with('sections')->orderBy('sort_order')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();

        return view('school.routine.create', compact('classes', 'subjects', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'day_of_week' => ['required', 'integer', 'min:1', 'max:7'],
            'starts_at' => ['required'],
            'ends_at' => ['required', 'after:starts_at'],
        ]);

        RoutinePeriod::create($data);

        return redirect()->route('school.routine.index', ['school_class_id' => $data['school_class_id']])
            ->with('status', 'Period added to routine.');
    }

    public function destroy(RoutinePeriod $routine): RedirectResponse
    {
        $classId = $routine->school_class_id;
        $routine->delete();

        return redirect()->route('school.routine.index', ['school_class_id' => $classId])
            ->with('status', 'Period removed.');
    }
}
