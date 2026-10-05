<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarkController extends Controller
{
    public function edit(ExamSubject $examSubject): View
    {
        $students = Student::where('school_class_id', $examSubject->school_class_id)
            ->where('status', 'active')
            ->orderBy('roll')
            ->get();

        $existing = Mark::where('exam_subject_id', $examSubject->id)
            ->get()
            ->keyBy('student_id');

        return view('school.marks.edit', compact('examSubject', 'students', 'existing'));
    }

    public function update(Request $request, ExamSubject $examSubject): RedirectResponse
    {
        $data = $request->validate([
            'marks' => ['required', 'array'],
            'marks.*' => ['nullable', 'numeric', 'min:0', 'max:'.$examSubject->full_marks],
            'absent' => ['array'],
        ]);

        $absentIds = array_keys($data['absent'] ?? []);

        foreach ($data['marks'] as $studentId => $value) {
            Mark::updateOrCreate(
                ['exam_subject_id' => $examSubject->id, 'student_id' => $studentId],
                [
                    'marks_obtained' => in_array($studentId, $absentIds) ? null : $value,
                    'is_absent' => in_array($studentId, $absentIds),
                ]
            );
        }

        return redirect()->route('school.exams.show', $examSubject->exam_id)->with('status', 'Marks saved.');
    }
}
