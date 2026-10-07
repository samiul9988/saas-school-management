<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AdmitInstruction;
use App\Models\Exam;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SeatPlanController extends Controller
{
    public function index(): View
    {
        $sections = Section::with('schoolClass')->get();
        $exams = Exam::orderByDesc('starts_on')->get();

        return view('school.exams.seat-plan', compact('sections', 'exams'));
    }

    public function downloadAdmitCard(Request $request)
    {
        [$section, $exam, $students] = $this->resolve($request);

        $school = Auth::user()->school;
        $instruction = AdmitInstruction::where('school_class_id', $section->school_class_id)
            ->where('exam_id', $exam->id)
            ->first();

        $pdf = Pdf::loadView('school.exams.pdf.admit-card', compact('school', 'section', 'exam', 'students', 'instruction'));

        return $pdf->download('admit-card.pdf');
    }

    public function downloadSeatPlan(Request $request, int $perPage)
    {
        [$section, $exam, $students] = $this->resolve($request);

        $perRoom = $perPage === 4 ? 4 : 10;

        $seats = $students->values()->map(function ($student, $index) use ($perRoom) {
            return [
                'student' => $student,
                'room' => intdiv($index, $perRoom) + 1,
                'seat_no' => $index % $perRoom + 1,
            ];
        });

        $school = Auth::user()->school;

        $pdf = Pdf::loadView('school.exams.pdf.seat-plan', compact('school', 'section', 'exam', 'seats', 'perRoom'));

        return $pdf->download("seat-plan-{$perRoom}-per-page.pdf");
    }

    private function resolve(Request $request): array
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'from_roll' => ['nullable', 'integer', 'min:0'],
            'to_roll' => ['nullable', 'integer', 'min:0'],
        ]);

        $section = Section::findOrFail($data['section_id']);
        $exam = Exam::findOrFail($data['exam_id']);

        $students = Student::where('section_id', $section->id)
            ->where('status', 'active')
            ->when($request->filled('from_roll') && (int) $request->from_roll > 0, fn ($q) => $q->where('roll', '>=', $request->from_roll))
            ->when($request->filled('to_roll') && (int) $request->to_roll > 0, fn ($q) => $q->where('roll', '<=', $request->to_roll))
            ->orderBy('roll')
            ->get();

        return [$section, $exam, $students];
    }
}
