<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TabulationSheetController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $exams = Exam::orderByDesc('starts_on')->get();

        $examSubjects = collect();
        $rows = collect();

        if ($request->filled('exam_id') && $request->filled('school_class_id')) {
            $exam = Exam::with(['examSubjects' => fn ($q) => $q->where('school_class_id', $request->school_class_id)->with('subject', 'marks')])
                ->findOrFail($request->exam_id);

            $examSubjects = $exam->examSubjects;

            $students = Student::where('school_class_id', $request->school_class_id)
                ->where('status', 'active')
                ->orderBy('roll')
                ->get();

            $rows = $students->map(function ($student) use ($examSubjects) {
                $totalFull = 0;
                $totalObtained = 0;
                $marksBySubject = [];

                foreach ($examSubjects as $examSubject) {
                    $mark = $examSubject->marks->firstWhere('student_id', $student->id);
                    $obtained = $mark && ! $mark->is_absent ? (float) $mark->marks_obtained : null;

                    $marksBySubject[$examSubject->id] = $obtained;
                    $totalFull += $examSubject->full_marks;
                    $totalObtained += $obtained ?? 0;
                }

                $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;

                return [
                    'student' => $student,
                    'marks' => $marksBySubject,
                    'total_obtained' => $totalObtained,
                    'total_full' => $totalFull,
                    'percentage' => $percentage,
                    'grade' => Grade::forPercent($percentage)?->name,
                ];
            })->sortByDesc('percentage')->values();
        }

        return view('school.exams.tabulation-sheet', compact('classes', 'exams', 'examSubjects', 'rows'));
    }
}
