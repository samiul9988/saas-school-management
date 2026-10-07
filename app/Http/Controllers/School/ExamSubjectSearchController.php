<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamSubjectSearchController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $exams = Exam::orderByDesc('starts_on')->get();
        $subjects = Subject::orderBy('name')->get();

        $results = collect();

        if ($request->filled('school_class_id') || $request->filled('exam_id') || $request->filled('subject_id')) {
            $results = ExamSubject::with('exam', 'schoolClass', 'subject')
                ->when($request->filled('school_class_id'), fn ($q) => $q->where('school_class_id', $request->school_class_id))
                ->when($request->filled('exam_id'), fn ($q) => $q->where('exam_id', $request->exam_id))
                ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->subject_id))
                ->get();
        }

        return view('school.exams.mark-search', compact('classes', 'exams', 'subjects', 'results'));
    }
}
