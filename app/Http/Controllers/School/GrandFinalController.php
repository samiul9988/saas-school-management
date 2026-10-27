<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\ResultBatch;
use App\Models\ResultBatchResult;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Support\ResultCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GrandFinalController extends Controller
{
    public function processPage(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $grandFinals = ResultBatch::where('type', ResultBatch::TYPE_GRAND_FINAL)->with('components.exam')->orderByDesc('id')->get();

        $class = null;
        $grandFinal = null;
        $rows = collect();

        if ($request->filled(['school_class_id', 'result_batch_id'])) {
            $data = $request->validate([
                'school_class_id' => ['required', 'exists:school_classes,id'],
                'result_batch_id' => ['required', 'exists:result_batches,id'],
            ]);

            $class = SchoolClass::findOrFail($data['school_class_id']);
            $grandFinal = ResultBatch::findOrFail($data['result_batch_id']);

            $rows = $grandFinal->results()
                ->where('school_class_id', $class->id)
                ->with('student', 'grade')
                ->orderBy('position')
                ->get();
        }

        return view('school.results.grand-final', compact('classes', 'grandFinals', 'class', 'grandFinal', 'rows'));
    }

    public function processClass(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'result_batch_id' => ['required', 'exists:result_batches,id'],
        ]);

        $class = SchoolClass::findOrFail($data['school_class_id']);
        $grandFinal = ResultBatch::findOrFail($data['result_batch_id']);
        abort_unless($grandFinal->type === ResultBatch::TYPE_GRAND_FINAL, 404);

        $students = Student::where('school_class_id', $class->id)->where('status', 'active')->orderByRoll()->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'No active students found for this class.');
        }

        $components = $grandFinal->components()->with('exam')->get();

        $perExamResults = $components->map(
            fn ($component) => ResultCalculator::forExam($component->exam, $class->id, $students)
        )->values();

        $combined = $students->mapWithKeys(function ($student) use ($components, $perExamResults) {
            $weightedPercentage = 0;
            $weightedGpa = 0;
            $totalWeight = 0;
            $allPassed = true;

            foreach ($components as $index => $component) {
                $examResult = $perExamResults[$index][$student->id] ?? null;

                if (! $examResult) {
                    $allPassed = false;

                    continue;
                }

                $weight = (float) $component->weight;
                $weightedPercentage += ($examResult['percentage'] * $weight) / 100;
                $weightedGpa += ($examResult['gpa'] * $weight) / 100;
                $totalWeight += $weight;

                if (! $examResult['is_passed']) {
                    $allPassed = false;
                }
            }

            $percentage = $totalWeight > 0 ? round($weightedPercentage, 2) : 0;
            $gpa = $totalWeight > 0 ? round($weightedGpa, 2) : 0;
            $grade = $allPassed ? Grade::forPercent($percentage) : Grade::where('grade_point', 0)->first();

            return [$student->id => [
                'student' => $student,
                'total_obtained' => $percentage,
                'total_full' => 100,
                'percentage' => $percentage,
                'gpa' => $allPassed ? $gpa : 0,
                'grade' => $grade,
                'is_passed' => $allPassed,
            ]];
        });

        $results = ResultCalculator::assignPositions($combined);

        DB::transaction(function () use ($results, $grandFinal, $class) {
            foreach ($results as $result) {
                ResultBatchResult::updateOrCreate(
                    ['result_batch_id' => $grandFinal->id, 'student_id' => $result['student']->id],
                    [
                        'school_class_id' => $class->id,
                        'section_id' => $result['student']->section_id,
                        'group_name' => $result['student']->group_name,
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

        return redirect()->route('school.results-process.grand-final', $request->only('school_class_id', 'result_batch_id'))
            ->with('status', 'Grand Final result processed for '.$results->count().' student(s).');
    }

    public const MERIT_TYPES = [
        ResultBatch::MERIT_TOTAL_MARK => 'Total Mark (Sequential)',
        ResultBatch::MERIT_GRADE_POINT => 'Grade Point (Sequential)',
    ];

    public function index(): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $exams = Exam::orderByDesc('starts_on')->get();
        $meritTypes = self::MERIT_TYPES;

        $grandFinals = ResultBatch::where('type', ResultBatch::TYPE_GRAND_FINAL)
            ->with('components.exam', 'schoolClass')
            ->orderByDesc('id')
            ->get();

        return view('school.results.grand-final-index', compact('classes', 'exams', 'meritTypes', 'grandFinals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'merit_process_type' => ['required', 'in:'.implode(',', array_keys(self::MERIT_TYPES))],
            'exam_ids' => ['required', 'array', 'min:1'],
            'exam_ids.*' => ['exists:exams,id'],
        ]);

        $class = SchoolClass::findOrFail($data['school_class_id']);
        $weight = round(100 / count($data['exam_ids']), 4);

        $this->saveGrandFinal($class, $data['merit_process_type'], array_fill_keys($data['exam_ids'], $weight));

        return redirect()->route('school.exam-configuration.index')->with('status', 'Grand Final configuration saved.');
    }

    public function storeMarkConfig(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'merit_process_type' => ['required', 'in:'.implode(',', array_keys(self::MERIT_TYPES))],
            'exam_ids' => ['required', 'array'],
            'exam_ids.*' => ['nullable', 'exists:exams,id'],
            'percentages' => ['required', 'array'],
            'percentages.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $class = SchoolClass::findOrFail($data['school_class_id']);

        $pairs = [];
        foreach ($data['exam_ids'] as $index => $examId) {
            if (! $examId) {
                continue;
            }

            $pairs[$examId] = (float) ($data['percentages'][$index] ?? 0);
        }

        if (empty($pairs)) {
            return back()->withErrors(['exam_ids' => 'Select at least one exam.'])->withInput();
        }

        $totalWeight = array_sum($pairs);

        if (abs($totalWeight - 100) > 0.5) {
            return back()->withErrors(['percentages' => 'Percentages must add up to 100%. Currently: '.$totalWeight.'%'])->withInput();
        }

        $this->saveGrandFinal($class, $data['merit_process_type'], $pairs);

        return redirect()->route('school.exam-configuration.grand-final')->with('status', 'Grand Final mark configuration saved.');
    }

    private function saveGrandFinal(SchoolClass $class, string $meritProcessType, array $examWeightPairs): void
    {
        DB::transaction(function () use ($class, $meritProcessType, $examWeightPairs) {
            $grandFinal = ResultBatch::updateOrCreate(
                ['type' => ResultBatch::TYPE_GRAND_FINAL, 'school_class_id' => $class->id],
                ['name' => $class->name.' Grand Final', 'merit_process_type' => $meritProcessType]
            );

            $grandFinal->components()->delete();

            foreach ($examWeightPairs as $examId => $weight) {
                $grandFinal->components()->create(['exam_id' => $examId, 'weight' => $weight]);
            }
        });
    }

    public function updateMeritType(Request $request, ResultBatch $grandFinal): RedirectResponse
    {
        abort_unless($grandFinal->type === ResultBatch::TYPE_GRAND_FINAL, 404);

        $data = $request->validate([
            'merit_process_type' => ['required', 'in:'.implode(',', array_keys(self::MERIT_TYPES))],
        ]);

        $grandFinal->update($data);

        return redirect()->route('school.exam-configuration.index')->with('status', 'Exam configuration updated.');
    }

    public function destroy(ResultBatch $grandFinal): RedirectResponse
    {
        abort_unless($grandFinal->type === ResultBatch::TYPE_GRAND_FINAL, 404);

        $grandFinal->delete();

        return redirect()->route('school.exam-configuration.index')->with('status', 'Grand Final removed.');
    }

    public function markConfigIndex(): View
    {
        $classes = SchoolClass::with(['grandFinal' => function ($query) {
            $query->with('components');
        }])->orderBy('sort_order')->get();

        $exams = Exam::orderByDesc('starts_on')->get();
        $meritTypes = self::MERIT_TYPES;

        return view('school.results.grand-final-mark-config', compact('classes', 'exams', 'meritTypes'));
    }
}
