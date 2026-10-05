<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(Request $request): View
    {
        $exams = Exam::orderByDesc('starts_on')->get();
        $classes = SchoolClass::orderBy('sort_order')->get();

        $rows = collect();

        if ($request->filled('exam_id') && $request->filled('school_class_id')) {
            $rows = $this->buildClassResult((int) $request->exam_id, (int) $request->school_class_id);
        }

        return view('school.results.index', compact('exams', 'classes', 'rows'));
    }

    public function marksheet(Exam $exam, Student $student): View
    {
        $examSubjects = $exam->examSubjects()
            ->where('school_class_id', $student->school_class_id)
            ->with(['subject', 'marks' => fn ($q) => $q->where('student_id', $student->id)])
            ->get();

        $totalFull = 0;
        $totalObtained = 0;
        $subjectRows = [];

        foreach ($examSubjects as $examSubject) {
            $mark = $examSubject->marks->first();
            $obtained = $mark?->is_absent ? null : $mark?->marks_obtained;

            $totalFull += $examSubject->full_marks;
            $totalObtained += $obtained ?? 0;

            $subjectRows[] = [
                'subject' => $examSubject->subject->name,
                'full_marks' => $examSubject->full_marks,
                'pass_marks' => $examSubject->pass_marks,
                'obtained' => $obtained,
                'is_absent' => (bool) $mark?->is_absent,
                'passed' => $obtained !== null && $obtained >= $examSubject->pass_marks,
            ];
        }

        $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $grade = Grade::forPercent($percentage);

        return view('school.results.marksheet', [
            'exam' => $exam,
            'student' => $student,
            'subjectRows' => $subjectRows,
            'totalFull' => $totalFull,
            'totalObtained' => $totalObtained,
            'percentage' => $percentage,
            'grade' => $grade,
        ]);
    }

    private function buildClassResult(int $examId, int $classId): \Illuminate\Support\Collection
    {
        $exam = Exam::with(['examSubjects.subject', 'examSubjects.marks'])->findOrFail($examId);
        $examSubjects = $exam->examSubjects->where('school_class_id', $classId);

        $students = Student::where('school_class_id', $classId)->where('status', 'active')->orderBy('roll')->get();

        $results = $students->map(function ($student) use ($examSubjects) {
            $totalFull = 0;
            $totalObtained = 0;
            $hasFailed = false;

            foreach ($examSubjects as $examSubject) {
                $mark = $examSubject->marks->firstWhere('student_id', $student->id);
                $obtained = $mark && ! $mark->is_absent ? (float) $mark->marks_obtained : 0;

                $totalFull += $examSubject->full_marks;
                $totalObtained += $obtained;

                if (! $mark || $mark->is_absent || $obtained < $examSubject->pass_marks) {
                    $hasFailed = true;
                }
            }

            $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;

            return [
                'student' => $student,
                'total_full' => $totalFull,
                'total_obtained' => $totalObtained,
                'percentage' => $percentage,
                'grade' => Grade::forPercent($percentage)?->name,
                'status' => $hasFailed ? 'Fail' : 'Pass',
            ];
        })->sortByDesc('percentage')->values();

        return $results->map(function ($row, $index) {
            $row['position'] = $index + 1;

            return $row;
        });
    }
}
