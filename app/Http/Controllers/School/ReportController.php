<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\ResultBatch;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function unassignedMarks(Request $request): View
    {
        $exams = Exam::orderByDesc('starts_on')->get();
        $rows = collect();
        $exam = null;

        if ($request->filled('exam_id')) {
            $exam = Exam::with('examSubjects.subject', 'examSubjects.schoolClass')->findOrFail($request->exam_id);

            $rows = $exam->examSubjects->map(function ($examSubject) {
                $totalStudents = \App\Models\Student::where('school_class_id', $examSubject->school_class_id)
                    ->where('status', 'active')
                    ->count();

                $markedCount = $examSubject->marks()
                    ->where(fn ($q) => $q->whereNotNull('marks_obtained')->orWhere('is_absent', true))
                    ->count();

                return [
                    'class' => $examSubject->schoolClass?->name,
                    'subject' => $examSubject->subject?->name,
                    'total_students' => $totalStudents,
                    'marked' => $markedCount,
                    'missing' => max($totalStudents - $markedCount, 0),
                    'exam_subject_id' => $examSubject->id,
                ];
            })->filter(fn ($row) => $row['missing'] > 0)->values();
        }

        return view('school.reports.unassigned-marks', compact('exams', 'exam', 'rows'));
    }

    public function sectionResult(Request $request): View
    {
        return $this->renderList($request, 'section', false, 'roll', 'Section Wise Result');
    }

    public function sectionMerit(Request $request): View
    {
        return $this->renderList($request, 'section', false, 'merit', 'Section Wise Merit List');
    }

    public function classMerit(Request $request): View
    {
        return $this->renderList($request, 'class', false, 'merit', 'Class Wise Merit List');
    }

    public function sectionFailed(Request $request): View
    {
        return $this->renderList($request, 'section', true, 'roll', 'Section Wise Fail List');
    }

    public function classFailed(Request $request): View
    {
        return $this->renderList($request, 'class', true, 'roll', 'Class Wise Fail List');
    }

    private function renderList(Request $request, string $scopeType, bool $failedOnly, string $sort, string $title): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $classes = SchoolClass::orderBy('sort_order')->get();
        $sections = Section::with('schoolClass')->get();

        $exams = Exam::when($request->filled('academic_year_id'), fn ($q) => $q->where('academic_year_id', $request->academic_year_id))
            ->orderByDesc('starts_on')
            ->get();

        $rows = collect();
        $resultBatch = null;

        $scopeRequestField = $scopeType === 'class' ? 'school_class_id' : 'section_id';

        if ($request->filled(['exam_id', $scopeRequestField])) {
            $data = $request->validate([
                'exam_id' => ['required', 'exists:exams,id'],
                $scopeRequestField => ['required'],
            ]);

            $resultBatch = ResultBatch::where('type', ResultBatch::TYPE_EXAM)->where('exam_id', $data['exam_id'])->first();

            if ($resultBatch) {
                $query = $resultBatch->results()->with('student', 'grade')
                    ->where($scopeRequestField, $data[$scopeRequestField]);

                if ($failedOnly) {
                    $query->where('is_passed', false);
                }

                $rows = $query->get();

                $rows = $sort === 'merit'
                    ? $rows->sortBy([
                        fn ($a, $b) => $b->gpa <=> $a->gpa,
                        fn ($a, $b) => $b->percentage <=> $a->percentage,
                    ])->values()
                    : $rows->sortBy(fn ($row) => (int) $row->student->roll)->values();
            }
        }

        return view('school.reports.list', compact(
            'title', 'scopeType', 'sort', 'failedOnly', 'academicYears', 'exams', 'classes', 'sections', 'rows', 'resultBatch'
        ));
    }

    public function sectionWiseGradingSummary(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $exams = Exam::when($request->filled('academic_year_id'), fn ($q) => $q->where('academic_year_id', $request->academic_year_id))
            ->orderByDesc('starts_on')
            ->get();

        $grades = Grade::orderByDesc('min_percent')->get();
        $summary = collect();
        $resultBatch = null;
        $exam = null;

        if ($request->filled('exam_id')) {
            $exam = Exam::findOrFail($request->exam_id);
            $resultBatch = ResultBatch::where('type', ResultBatch::TYPE_EXAM)->where('exam_id', $exam->id)->first();

            if ($resultBatch) {
                $results = $resultBatch->results()->with('section.schoolClass')->get();

                $summary = $results->groupBy('section_id')->map(function ($sectionResults) use ($grades) {
                    $section = $sectionResults->first()->section;

                    $counts = $grades->mapWithKeys(fn ($grade) => [
                        $grade->id => $sectionResults->where('grade_id', $grade->id)->count(),
                    ]);

                    return [
                        'section' => $section,
                        'counts' => $counts,
                        'total' => $sectionResults->count(),
                    ];
                })->values();
            }
        }

        return view('school.reports.grading-summary', compact('academicYears', 'exams', 'grades', 'summary', 'resultBatch', 'exam'));
    }

    public function sectionWisePassFailSummary(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $exams = Exam::when($request->filled('academic_year_id'), fn ($q) => $q->where('academic_year_id', $request->academic_year_id))
            ->orderByDesc('starts_on')
            ->get();

        $summary = collect();
        $resultBatch = null;
        $exam = null;

        if ($request->filled('exam_id')) {
            $exam = Exam::findOrFail($request->exam_id);
            $resultBatch = ResultBatch::where('type', ResultBatch::TYPE_EXAM)->where('exam_id', $exam->id)->first();

            if ($resultBatch) {
                $results = $resultBatch->results()->with('section.schoolClass')->get();

                $summary = $results->groupBy('section_id')->map(function ($sectionResults) {
                    $section = $sectionResults->first()->section;
                    $total = $sectionResults->count();
                    $passed = $sectionResults->where('is_passed', true)->count();
                    $failed = $total - $passed;

                    return [
                        'section' => $section,
                        'total' => $total,
                        'passed' => $passed,
                        'failed' => $failed,
                        'pass_percent' => $total > 0 ? round(($passed / $total) * 100, 2) : 0,
                    ];
                })->values();
            }
        }

        return view('school.reports.pass-fail-summary', compact('academicYears', 'exams', 'summary', 'resultBatch', 'exam'));
    }
}
