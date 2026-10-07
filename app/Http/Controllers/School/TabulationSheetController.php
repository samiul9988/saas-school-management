<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TabulationSheetController extends Controller
{
    public function index(): View
    {
        $exams = Exam::orderByDesc('starts_on')->get();

        return view('school.exams.tabulation-sheet', compact('exams'));
    }

    public function download(Request $request)
    {
        $data = $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
        ]);

        $exam = Exam::with('examSubjects.subject', 'examSubjects.marks')->findOrFail($data['exam_id']);
        $school = Auth::user()->school;

        $classIds = $exam->examSubjects->pluck('school_class_id')->unique();
        $classes = SchoolClass::whereIn('id', $classIds)->orderBy('sort_order')->get();

        $classBlocks = $classes->map(function ($class) use ($exam) {
            $examSubjects = $exam->examSubjects->where('school_class_id', $class->id);

            $students = Student::where('school_class_id', $class->id)->where('status', 'active')->orderBy('roll')->get();

            $rows = $students->map(function ($student) use ($examSubjects) {
                $totalFull = 0;
                $totalObtained = 0;
                $marks = [];

                foreach ($examSubjects as $examSubject) {
                    $mark = $examSubject->marks->firstWhere('student_id', $student->id);
                    $obtained = $mark && ! $mark->is_absent ? (float) $mark->marks_obtained : null;
                    $marks[$examSubject->id] = $obtained;
                    $totalFull += $examSubject->full_marks;
                    $totalObtained += $obtained ?? 0;
                }

                $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;

                return [
                    'student' => $student,
                    'marks' => $marks,
                    'total_obtained' => $totalObtained,
                    'total_full' => $totalFull,
                    'percentage' => $percentage,
                    'grade' => Grade::forPercent($percentage)?->name,
                ];
            })->sortByDesc('percentage')->values();

            return ['class' => $class, 'examSubjects' => $examSubjects, 'rows' => $rows];
        });

        $pdf = Pdf::loadView('school.exams.pdf.tabulation-sheet', compact('school', 'exam', 'classBlocks'))->setPaper('a4', 'landscape');

        return $pdf->download('tabulation-sheet.pdf');
    }
}
