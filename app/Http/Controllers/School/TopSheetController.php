<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TopSheetController extends Controller
{
    public function index(): View
    {
        $sections = Section::with('schoolClass')->get();
        $exams = Exam::orderByDesc('starts_on')->get();

        return view('school.exams.top-sheet', compact('sections', 'exams'));
    }

    public function download(Request $request)
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id' => ['required', 'exists:exams,id'],
        ]);

        $section = Section::findOrFail($data['section_id']);
        $exam = Exam::findOrFail($data['exam_id']);

        $students = Student::where('section_id', $section->id)
            ->where('status', 'active')
            ->orderByRoll()
            ->get();

        $school = Auth::user()->school;

        $pdf = Pdf::loadView('school.exams.pdf.top-sheet', [
            'school' => $school,
            'section' => $section,
            'exam' => $exam,
            'students' => $students,
        ]);

        return $pdf->download('top-sheet.pdf');
    }
}
