<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamShortCode;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectAssignment;
use App\Models\SubjectComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MarkConfigurationController extends Controller
{
    public const GROUPS = ['Science', 'Commerce', 'Arts', 'General'];

    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $groups = self::GROUPS;
        $shortCodes = ExamShortCode::orderBy('serial')->get();
        $exams = Exam::orderByDesc('starts_on')->get();

        $class = null;
        $groupName = null;
        $availableSubjects = collect();

        if ($request->filled(['school_class_id', 'group_name'])) {
            $data = $request->validate([
                'school_class_id' => ['required', 'exists:school_classes,id'],
                'group_name' => ['required', 'string'],
            ]);

            $class = SchoolClass::findOrFail($data['school_class_id']);
            $groupName = $data['group_name'];

            $assignedSubjectIds = SubjectAssignment::where('school_class_id', $class->id)
                ->where('group_name', $groupName)
                ->pluck('subject_id');

            $availableSubjects = $assignedSubjectIds->isNotEmpty()
                ? Subject::whereIn('id', $assignedSubjectIds)->orderBy('name')->get()
                : Subject::where('school_class_id', $class->id)->orderBy('name')->get();

            // Prefill Step 3 from the first available subject's existing short-code setup, if any.
            $prefillComponents = $availableSubjects->isNotEmpty()
                ? SubjectComponent::where('subject_id', $availableSubjects->first()->id)->get()->keyBy('exam_short_code_id')
                : collect();

            $shortCodes = $shortCodes->map(function ($shortCode) use ($prefillComponents) {
                $existing = $prefillComponents->get($shortCode->id);
                $shortCode->prefill_total = $existing->total ?? 100;
                $shortCode->prefill_pass_mark = $existing->pass_mark ?? 0;
                $shortCode->prefill_acceptance = $existing->acceptance ?? 1;

                return $shortCode;
            });
        }

        // Bottom "view existing configuration" search
        $viewClass = null;
        $viewGroupName = null;
        $viewExam = null;
        $existingRows = collect();

        if ($request->filled(['view_school_class_id', 'view_group_name', 'view_exam_id'])) {
            $viewData = $request->validate([
                'view_school_class_id' => ['required', 'exists:school_classes,id'],
                'view_group_name' => ['required', 'string'],
                'view_exam_id' => ['required', 'exists:exams,id'],
            ]);

            $viewClass = SchoolClass::findOrFail($viewData['view_school_class_id']);
            $viewGroupName = $viewData['view_group_name'];
            $viewExam = Exam::findOrFail($viewData['view_exam_id']);

            $subjectIdsForExam = ExamSubject::where('exam_id', $viewExam->id)
                ->where('school_class_id', $viewClass->id)
                ->pluck('subject_id');

            $assignedSubjectIds = SubjectAssignment::where('school_class_id', $viewClass->id)
                ->where('group_name', $viewGroupName)
                ->pluck('subject_id');

            if ($assignedSubjectIds->isNotEmpty()) {
                $subjectIdsForExam = $subjectIdsForExam->intersect($assignedSubjectIds);
            }

            $existingRows = SubjectComponent::whereIn('subject_id', $subjectIdsForExam)
                ->with('subject', 'shortCode')
                ->orderBy('subject_id')
                ->orderBy('serial')
                ->get();
        }

        return view('school.subjects.mark-configuration', compact(
            'classes', 'groups', 'shortCodes', 'exams',
            'class', 'groupName', 'availableSubjects',
            'viewClass', 'viewGroupName', 'viewExam', 'existingRows'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'group_name' => ['required', 'string'],
            'subject_id' => ['required', 'array', 'min:1'],
            'subject_id.*' => ['exists:subjects,id'],
            'exam_id' => ['required', 'array', 'min:1'],
            'exam_id.*' => ['exists:exams,id'],
            'exam_short_code_id' => ['required', 'array', 'min:1'],
            'exam_short_code_id.*' => ['exists:exam_short_codes,id'],
            'total' => ['required', 'array'],
            'total.*' => ['required', 'numeric', 'min:0'],
            'pass_mark' => ['required', 'array'],
            'pass_mark.*' => ['required', 'numeric', 'min:0'],
            'acceptance' => ['required', 'array'],
            'acceptance.*' => ['required', 'numeric', 'min:0'],
        ]);

        $class = SchoolClass::findOrFail($data['school_class_id']);
        $totalMarks = array_sum($data['total']);
        $totalPassMarks = array_sum($data['pass_mark']);

        DB::transaction(function () use ($data, $class, $totalMarks, $totalPassMarks) {
            foreach ($data['subject_id'] as $subjectId) {
                foreach ($data['exam_short_code_id'] as $i => $shortCodeId) {
                    SubjectComponent::updateOrCreate(
                        ['subject_id' => $subjectId, 'exam_short_code_id' => $shortCodeId],
                        [
                            'total' => $data['total'][$i],
                            'pass_mark' => $data['pass_mark'][$i],
                            'acceptance' => $data['acceptance'][$i],
                        ]
                    );
                }

                foreach ($data['exam_id'] as $examId) {
                    ExamSubject::updateOrCreate(
                        ['exam_id' => $examId, 'school_class_id' => $class->id, 'subject_id' => $subjectId],
                        ['full_marks' => $totalMarks, 'pass_marks' => $totalPassMarks]
                    );
                }
            }
        });

        return redirect()->route('school.exam-configuration.mark', $request->only('school_class_id', 'group_name'))
            ->with('status', 'Mark configuration saved.');
    }
}
