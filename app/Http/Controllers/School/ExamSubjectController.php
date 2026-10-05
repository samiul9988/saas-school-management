<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamSubjectController extends Controller
{
    public function create(Exam $exam): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('school.exams.subjects.create', compact('exam', 'classes', 'subjects'));
    }

    public function store(Request $request, Exam $exam): RedirectResponse
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'full_marks' => ['required', 'numeric', 'min:1'],
            'pass_marks' => ['required', 'numeric', 'min:0', 'lte:full_marks'],
            'exam_date' => ['nullable', 'date'],
        ]);

        $data['exam_id'] = $exam->id;

        ExamSubject::create($data);

        return redirect()->route('school.exams.show', $exam)->with('status', 'Subject assigned to exam.');
    }

    public function destroy(Exam $exam, ExamSubject $examSubject): RedirectResponse
    {
        $examSubject->delete();

        return redirect()->route('school.exams.show', $exam)->with('status', 'Subject removed from exam.');
    }
}
