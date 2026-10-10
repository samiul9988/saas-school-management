<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\FourthSubjectAssignment;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FourthSubjectController extends Controller
{
    public const GROUPS = ['Science', 'Commerce', 'Arts', 'General'];

    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'assign') === 'list' ? 'list' : 'assign';

        $sections = Section::with('schoolClass')->orderBy('name')->get();
        $groups = self::GROUPS;

        $section = null;
        $groupName = null;
        $students = collect();
        $subjects = collect();
        $existingBySubject = [];

        if ($tab === 'assign' && $request->filled(['section_id', 'group_name'])) {
            $data = $request->validate([
                'section_id' => ['required', 'exists:sections,id'],
                'group_name' => ['required', 'string'],
            ]);

            $section = Section::findOrFail($data['section_id']);
            $groupName = $data['group_name'];

            $students = Student::where('section_id', $section->id)
                ->where('group_name', $groupName)
                ->orderBy('roll')
                ->get();

            $subjects = Subject::where('school_class_id', $section->school_class_id)
                ->orderBy('name')
                ->get();

            $existingBySubject = FourthSubjectAssignment::whereIn('student_id', $students->pluck('id'))
                ->get()
                ->groupBy('subject_type')
                ->map(fn ($rows) => $rows->pluck('subject_id')->unique()->all())
                ->all();
        }

        $assignments = collect();
        if ($tab === 'list' && $request->filled('section_id')) {
            $data = $request->validate([
                'section_id' => ['required', 'exists:sections,id'],
            ]);

            $section = Section::findOrFail($data['section_id']);

            $assignments = FourthSubjectAssignment::with(['student', 'subject'])
                ->where('section_id', $section->id)
                ->orderBy('id')
                ->get();
        }

        return view('school.subjects.fourth-subject', compact(
            'tab', 'sections', 'groups', 'section', 'groupName', 'students', 'subjects', 'existingBySubject', 'assignments'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'group_name' => ['required', 'string'],
            'student_ids' => ['array'],
            'student_ids.*' => ['exists:students,id'],
            'compulsory_subject_ids' => ['array'],
            'compulsory_subject_ids.*' => ['exists:subjects,id'],
            'optional_subject_ids' => ['array'],
            'optional_subject_ids.*' => ['exists:subjects,id'],
        ]);

        $studentIds = $data['student_ids'] ?? [];
        $section = Section::findOrFail($data['section_id']);

        if (empty($studentIds)) {
            return redirect()->route('school.exam-configuration.fourth-subject', $request->only('section_id', 'group_name'))
                ->with('status', 'Select at least one student to assign a subject.');
        }

        $subjectTypeMap = [];
        foreach ($data['compulsory_subject_ids'] ?? [] as $subjectId) {
            $subjectTypeMap[$subjectId] = 'Compulsory';
        }
        foreach ($data['optional_subject_ids'] ?? [] as $subjectId) {
            $subjectTypeMap[$subjectId] = 'Optional';
        }

        DB::transaction(function () use ($studentIds, $subjectTypeMap, $section, $data) {
            foreach ($studentIds as $studentId) {
                FourthSubjectAssignment::where('student_id', $studentId)->delete();

                foreach ($subjectTypeMap as $subjectId => $subjectType) {
                    FourthSubjectAssignment::create([
                        'school_class_id' => $section->school_class_id,
                        'section_id' => $section->id,
                        'group_name' => $data['group_name'],
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                        'subject_type' => $subjectType,
                    ]);
                }
            }
        });

        return redirect()->route('school.exam-configuration.fourth-subject', $request->only('section_id', 'group_name'))
            ->with('status', '4th subject assigned.');
    }

    public function destroy(FourthSubjectAssignment $fourthSubjectAssignment): RedirectResponse
    {
        $fourthSubjectAssignment->delete();

        return redirect()->route('school.exam-configuration.fourth-subject', ['tab' => 'list'])
            ->with('status', '4th subject assignment removed.');
    }
}
