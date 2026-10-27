<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\RemarkTemplate;
use App\Models\ResultBatch;
use App\Models\ResultBatchResult;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Support\ResultCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ResultProcessController extends Controller
{
    public const GROUPS = ['Science', 'Commerce', 'Arts', 'General'];

    public function generalExam(Request $request): View
    {
        $sections = Section::with('schoolClass')->get();
        $exams = Exam::orderByDesc('starts_on')->get();
        $groups = self::GROUPS;

        $section = null;
        $resultBatch = null;
        $rows = collect();

        if ($request->filled(['section_id', 'exam_id'])) {
            $data = $request->validate([
                'section_id' => ['required', 'exists:sections,id'],
                'exam_id' => ['required', 'exists:exams,id'],
                'group_name' => ['nullable', 'string'],
            ]);

            $section = Section::with('schoolClass')->findOrFail($data['section_id']);

            $resultBatch = ResultBatch::where('type', ResultBatch::TYPE_EXAM)
                ->where('exam_id', $data['exam_id'])
                ->first();

            if ($resultBatch) {
                $rows = $resultBatch->results()
                    ->where('section_id', $section->id)
                    ->when($request->filled('group_name'), fn ($q) => $q->where('group_name', $data['group_name']))
                    ->with('student', 'grade')
                    ->orderBy('position')
                    ->get();
            }
        }

        return view('school.results.general-exam', compact('sections', 'exams', 'groups', 'section', 'resultBatch', 'rows'));
    }

    public function processGeneralExam(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'group_name' => ['nullable', 'string'],
        ]);

        $section = Section::findOrFail($data['section_id']);
        $exam = Exam::findOrFail($data['exam_id']);

        $students = Student::where('section_id', $section->id)
            ->where('status', 'active')
            ->when($request->filled('group_name'), fn ($q) => $q->where('group_name', $data['group_name']))
            ->orderByRoll()
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'No active students found for this selection.');
        }

        $resultBatch = ResultBatch::firstOrCreate(
            ['type' => ResultBatch::TYPE_EXAM, 'exam_id' => $exam->id],
            ['name' => $exam->name]
        );

        $results = ResultCalculator::assignPositions(
            ResultCalculator::forExam($exam, $section->school_class_id, $students)
        );

        DB::transaction(function () use ($results, $resultBatch, $section, $data) {
            foreach ($results as $result) {
                ResultBatchResult::updateOrCreate(
                    ['result_batch_id' => $resultBatch->id, 'student_id' => $result['student']->id],
                    [
                        'school_class_id' => $section->school_class_id,
                        'section_id' => $section->id,
                        'group_name' => $data['group_name'] ?? null,
                        'total_obtained' => $result['total_obtained'],
                        'total_full' => $result['total_full'],
                        'percentage' => $result['percentage'],
                        'gpa' => $result['gpa'],
                        'grade_id' => $result['grade']?->id,
                        'position' => $result['position'],
                        'is_passed' => $result['is_passed'],
                    ]
                );
            }
        });

        return redirect()->route('school.results-process.general-exam', $request->only('section_id', 'exam_id', 'group_name'))
            ->with('status', 'Result processed for '.$results->count().' student(s).');
    }

    public function meritPosition(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $batches = ResultBatch::with('exam')->orderByDesc('id')->get();

        $rows = collect();
        $resultBatch = null;
        $class = null;

        if ($request->filled(['result_batch_id', 'school_class_id'])) {
            $data = $request->validate([
                'result_batch_id' => ['required', 'exists:result_batches,id'],
                'school_class_id' => ['required', 'exists:school_classes,id'],
            ]);

            $resultBatch = ResultBatch::with('exam')->findOrFail($data['result_batch_id']);
            $class = SchoolClass::findOrFail($data['school_class_id']);

            $rows = $resultBatch->results()
                ->where('school_class_id', $class->id)
                ->with('student', 'grade')
                ->get()
                ->sortBy([
                    fn ($a, $b) => $b->gpa <=> $a->gpa,
                    fn ($a, $b) => $b->percentage <=> $a->percentage,
                ])->values();
        }

        return view('school.results.merit-position', compact('classes', 'batches', 'rows', 'resultBatch', 'class'));
    }

    public function giveRemarks(Request $request): View
    {
        $sections = Section::with('schoolClass')->get();
        $batches = ResultBatch::with('exam')->orderByDesc('id')->get();
        $remarkTemplates = RemarkTemplate::orderBy('name')->get();

        $rows = collect();
        $resultBatch = null;
        $section = null;

        if ($request->filled(['result_batch_id', 'section_id'])) {
            $data = $request->validate([
                'result_batch_id' => ['required', 'exists:result_batches,id'],
                'section_id' => ['required', 'exists:sections,id'],
            ]);

            $resultBatch = ResultBatch::with('exam')->findOrFail($data['result_batch_id']);
            $section = Section::with('schoolClass')->findOrFail($data['section_id']);

            $rows = $resultBatch->results()
                ->where('section_id', $section->id)
                ->with('student', 'grade')
                ->orderBy('position')
                ->get();
        }

        return view('school.results.give-remarks', compact('sections', 'batches', 'remarkTemplates', 'rows', 'resultBatch', 'section'));
    }

    public function storeRemarkTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        RemarkTemplate::create($data);

        return back()->with('status', 'Remark template added.');
    }

    public function saveRemarks(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'result_batch_id' => ['required', 'exists:result_batches,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'remarks' => ['required', 'string', 'max:255'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        ResultBatchResult::where('result_batch_id', $data['result_batch_id'])
            ->whereIn('student_id', $data['student_ids'])
            ->update(['remarks' => $data['remarks']]);

        return redirect()->route('school.results-process.give-remarks', $request->only('result_batch_id', 'section_id'))
            ->with('status', 'Remarks saved for '.count($data['student_ids']).' student(s).');
    }
}
