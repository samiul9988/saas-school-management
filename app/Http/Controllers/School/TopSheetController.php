<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopSheetController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $exams = Exam::orderByDesc('starts_on')->get();
        $subjects = Subject::orderBy('name')->get();

        $students = collect();
        $examSubject = null;

        if ($request->filled(['exam_id', 'school_class_id', 'subject_id'])) {
            $examSubject = ExamSubject::with('exam', 'schoolClass', 'subject')
                ->where('exam_id', $request->exam_id)
                ->where('school_class_id', $request->school_class_id)
                ->where('subject_id', $request->subject_id)
                ->first();

            $students = Student::where('school_class_id', $request->school_class_id)
                ->where('status', 'active')
                ->orderBy('roll')
                ->get();
        }

        return view('school.exams.top-sheet', compact('classes', 'exams', 'subjects', 'students', 'examSubject'));
    }
}
