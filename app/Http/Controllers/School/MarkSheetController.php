<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\MarksheetTemplateConfig;
use App\Models\ResultBatch;
use App\Models\Section;
use App\Models\Student;
use App\Support\ResultCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MarkSheetController extends Controller
{
    public function generalExam(): View
    {
        $sections = Section::with('schoolClass')->get();
        $exams = Exam::orderByDesc('starts_on')->get();

        return view('school.mark-sheets.general-exam', compact('sections', 'exams'));
    }

    public function downloadGeneralExam(Request $request)
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'from_roll' => ['nullable', 'integer', 'min:0'],
            'to_roll' => ['nullable', 'integer', 'min:0'],
        ]);

        $section = Section::with('schoolClass')->findOrFail($data['section_id']);
        $exam = Exam::findOrFail($data['exam_id']);
        $school = Auth::user()->school;

        $students = Student::where('section_id', $section->id)
            ->where('status', 'active')
            ->when($request->filled('from_roll') && (int) $request->from_roll > 0, fn ($q) => $q->where('roll', '>=', $request->from_roll))
            ->when($request->filled('to_roll') && (int) $request->to_roll > 0, fn ($q) => $q->where('roll', '<=', $request->to_roll))
            ->orderByRoll()
            ->get();

        $results = ResultCalculator::forExam($exam, $section->school_class_id, $students);

        $templateConfig = MarksheetTemplateConfig::where('school_class_id', $section->school_class_id)->first();
        $templateNumber = $templateConfig->template_number ?? 1;
        $grades = Grade::orderByDesc('min_percent')->get();

        $examSubjects = $exam->examSubjects()->where('school_class_id', $section->school_class_id)->with('subject', 'marks.components.subjectComponent.shortCode')->get();

        $highestBySubject = $examSubjects->mapWithKeys(function ($examSubject) {
            $highest = $examSubject->marks->where('is_absent', false)->max('marks_obtained');

            return [$examSubject->id => $highest ?? 0];
        });

        $resultBatch = ResultBatch::where('type', ResultBatch::TYPE_EXAM)->where('exam_id', $exam->id)->first();
        $processedResults = $resultBatch
            ? $resultBatch->results()->whereIn('student_id', $students->pluck('id'))->get()->keyBy('student_id')
            : collect();

        $pages = $students->map(function ($student) use ($results, $exam, $section, $examSubjects, $highestBySubject, $templateNumber, $grades, $processedResults) {
            $result = $results[$student->id] ?? null;

            $subjectRows = $examSubjects->map(function ($examSubject) use ($student, $highestBySubject) {
                $mark = $examSubject->marks->firstWhere('student_id', $student->id);
                $isAbsent = ! $mark || $mark->is_absent;
                $obtained = $isAbsent ? 0 : (float) $mark->marks_obtained;
                $percent = $examSubject->full_marks > 0 ? ($obtained / $examSubject->full_marks) * 100 : 0;
                $grade = Grade::forPercent($percent);

                $components = $mark?->components->map(fn ($component) => [
                    'label' => $component->subjectComponent?->shortCode?->name ?? '-',
                    'value' => $isAbsent ? '-' : $component->marks_obtained,
                ])->all() ?? [];

                return [
                    'name' => $examSubject->subject?->name,
                    'full' => $examSubject->full_marks,
                    'highest' => $highestBySubject[$examSubject->id] ?? 0,
                    'components' => $components,
                    'total' => $isAbsent ? 'Absent' : $obtained,
                    'letterGrade' => $grade?->name ?? 'F',
                    'gradePoint' => $grade ? rtrim(rtrim(number_format($grade->grade_point, 2), '0'), '.') : '0',
                ];
            })->values()->all();

            $processed = $processedResults->get($student->id);

            $qr = base64_encode(QrCode::format('png')->size(100)->margin(0)->generate("Student:{$student->id}|Roll:{$student->roll}|{$student->name}"));

            return [
                'variant' => $templateNumber,
                'school' => Auth::user()->school,
                'student' => [
                    'name' => $student->name,
                    'father_name' => $student->father_name,
                    'mother_name' => $student->mother_name,
                    'id' => $student->admission_number ?? $student->id,
                    'roll' => $student->roll,
                    'class' => $section->schoolClass?->name,
                    'section' => $section->name,
                    'group_name' => $student->group_name,
                    'contact' => $student->guardian_phone,
                    'dob' => $student->date_of_birth?->format('d-M-Y'),
                    'photo' => null,
                ],
                'examName' => $exam->name,
                'yearSession' => $exam->academicYear?->name ?? now()->year,
                'subjectRows' => $subjectRows,
                'totals' => [
                    'full' => $result['total_full'] ?? 0,
                    'obtained' => $result['total_obtained'] ?? 0,
                    'gpa' => $result['gpa'] ?? 0,
                    'letterGrade' => $result['grade']?->name ?? 'F',
                    'resultStatus' => ($result['is_passed'] ?? false) ? 'Passed' : 'Failed',
                ],
                'meta' => [
                    'failedSubjects' => collect($subjectRows)->filter(fn ($row) => $row['letterGrade'] === 'F')->count(),
                    'classPosition' => $processed?->position,
                    'shiftPosition' => $processed?->position,
                    'sectionPosition' => $processed?->position,
                    'workingDays' => null,
                    'totalPresent' => null,
                    'totalAbsent' => null,
                    'totalStudents' => $processedResults->count() ?: null,
                ],
                'grades' => $grades,
                'qrDataUri' => 'data:image/png;base64,'.$qr,
                'publishDate' => now()->format('d-M-y'),
                'forPdf' => true,
            ];
        });

        $pdf = Pdf::loadView('school.mark-sheets.pdf.general-exam-batch', compact('pages'));

        return $pdf->download("marksheet-{$exam->name}-{$section->name}.pdf");
    }

    public function grandFinal(Request $request): View
    {
        $sections = Section::with('schoolClass')->get();

        $section = $request->filled('section_id')
            ? Section::with('schoolClass')->findOrFail($request->section_id)
            : null;

        $grandFinals = ResultBatch::where('type', ResultBatch::TYPE_GRAND_FINAL)
            ->with('components.exam')
            ->orderByDesc('id')
            ->get();

        return view('school.mark-sheets.grand-final', compact('sections', 'section', 'grandFinals'));
    }

    public function downloadGrandFinal(Request $request, ResultBatch $resultBatch)
    {
        abort_unless($resultBatch->type === ResultBatch::TYPE_GRAND_FINAL, 404);

        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'from_roll' => ['nullable', 'integer', 'min:0'],
            'to_roll' => ['nullable', 'integer', 'min:0'],
        ]);

        $section = Section::with('schoolClass')->findOrFail($data['section_id']);
        $school = Auth::user()->school;

        $students = Student::where('section_id', $section->id)
            ->where('status', 'active')
            ->when($request->filled('from_roll') && (int) $request->from_roll > 0, fn ($q) => $q->where('roll', '>=', $request->from_roll))
            ->when($request->filled('to_roll') && (int) $request->to_roll > 0, fn ($q) => $q->where('roll', '<=', $request->to_roll))
            ->orderByRoll()
            ->get();

        $components = $resultBatch->components()->with('exam')->get();

        $combinedResults = $resultBatch->results()->where('section_id', $section->id)->with('grade')->get()->keyBy('student_id');

        $sheets = $students->map(function ($student) use ($components, $combinedResults) {
            $componentRows = $components->map(function ($component) use ($student) {
                $examResults = ResultCalculator::forExam($component->exam, $student->school_class_id, collect([$student]));
                $result = $examResults[$student->id] ?? null;

                return [
                    'exam' => $component->exam,
                    'weight' => $component->weight,
                    'total_obtained' => $result['total_obtained'] ?? 0,
                    'total_full' => $result['total_full'] ?? 0,
                    'percentage' => $result['percentage'] ?? 0,
                    'gpa' => $result['gpa'] ?? 0,
                    'is_passed' => $result['is_passed'] ?? false,
                ];
            });

            return [
                'student' => $student,
                'componentRows' => $componentRows,
                'combined' => $combinedResults->get($student->id),
            ];
        });

        $pdf = Pdf::loadView('school.mark-sheets.pdf.grand-final', compact('school', 'section', 'resultBatch', 'sheets'));

        return $pdf->download("grand-final-marksheet-{$resultBatch->name}-{$section->name}.pdf");
    }
}
