<?php

namespace App\Support;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Support\Collection;

class ResultCalculator
{
    /**
     * Compute subject-level and aggregate exam results for a set of students in one class.
     * Returns a keyed collection (student_id => result array).
     */
    public static function forExam(Exam $exam, int $schoolClassId, Collection $students): Collection
    {
        $examSubjects = $exam->examSubjects()
            ->where('school_class_id', $schoolClassId)
            ->with('marks')
            ->get();

        return $students->mapWithKeys(function (Student $student) use ($examSubjects) {
            $totalFull = 0;
            $totalObtained = 0;
            $gradePoints = [];
            $hasFailed = $examSubjects->isEmpty();
            $failedSubjectsCount = 0;

            foreach ($examSubjects as $examSubject) {
                $mark = $examSubject->marks->firstWhere('student_id', $student->id);
                $isAbsent = ! $mark || $mark->is_absent;
                $obtained = $isAbsent ? 0 : (float) $mark->marks_obtained;

                $totalFull += (float) $examSubject->full_marks;
                $totalObtained += $obtained;

                $subjectPassed = ! $isAbsent && $obtained >= $examSubject->pass_marks;

                if (! $subjectPassed) {
                    $hasFailed = true;
                    $failedSubjectsCount++;
                }

                $subjectPercent = $examSubject->full_marks > 0 ? ($obtained / $examSubject->full_marks) * 100 : 0;
                $gradePoints[] = $subjectPassed ? (float) (Grade::forPercent($subjectPercent)?->grade_point ?? 0) : 0;
            }

            $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
            $gpa = $hasFailed ? 0 : round(array_sum($gradePoints) / max(count($gradePoints), 1), 2);
            $grade = $hasFailed ? Grade::where('grade_point', 0)->first() : Grade::forPercent($percentage);

            return [$student->id => [
                'student' => $student,
                'total_obtained' => $totalObtained,
                'total_full' => $totalFull,
                'percentage' => $percentage,
                'gpa' => $gpa,
                'grade' => $grade,
                'is_passed' => ! $hasFailed,
                'failed_subjects_count' => $failedSubjectsCount,
            ]];
        });
    }

    /**
     * Assign competition-style positions (ties share a position) based on GPA desc, then percentage desc.
     */
    public static function assignPositions(Collection $rows): Collection
    {
        $sorted = $rows->sortBy([
            fn ($a, $b) => $b['gpa'] <=> $a['gpa'],
            fn ($a, $b) => $b['percentage'] <=> $a['percentage'],
        ])->values();

        $position = 0;
        $seen = 0;
        $lastKey = null;

        return $sorted->map(function ($row) use (&$position, &$seen, &$lastKey) {
            $seen++;
            $key = $row['gpa'].'|'.$row['percentage'];

            if ($key !== $lastKey) {
                $position = $seen;
                $lastKey = $key;
            }

            $row['position'] = $row['is_passed'] ? $position : null;

            return $row;
        });
    }
}
