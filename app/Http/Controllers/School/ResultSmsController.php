<?php

namespace App\Http\Controllers\School;

use App\Exceptions\Sms\SmsSendingException;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\SmsTemplate;
use App\Models\Student;
use App\Services\Sms\SmsSendingService;
use App\Services\Sms\SmsTemplateRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResultSmsController extends Controller
{
    public function index(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $classes = SchoolClass::orderBy('sort_order')->get();
        $exams = Exam::orderByDesc('starts_on')->get();
        $results = collect();

        if ($request->filled(['exam_id', 'school_class_id'])) {
            $results = $this->classResults($request->integer('exam_id'), $request->integer('school_class_id'));
        }

        $defaultContent = SmsTemplate::defaultContentFor('result');

        return view('school.sms-send.result', compact('academicYears', 'classes', 'exams', 'results', 'defaultContent'));
    }

    public function store(Request $request, SmsSendingService $sendingService, SmsTemplateRenderer $renderer): RedirectResponse
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $exam = Exam::findOrFail($data['exam_id']);
        $results = $this->classResults($data['exam_id'], $data['school_class_id'])
            ->whereIn('student.id', $data['student_ids']);

        $sent = 0;
        $failed = 0;

        foreach ($results as $result) {
            $student = $result['student'];

            if (! $student->guardian_phone) {
                continue;
            }

            $message = $renderer->render($data['content'], [
                'student_name' => $student->name,
                'class' => $student->schoolClass?->name,
                'exam_name' => $exam->name,
                'total_marks' => $result['total_obtained'],
                'grade' => $result['grade'],
                'gpa' => $result['gpa'],
            ]);

            try {
                $sendingService->send($request->user()->school, [$student->guardian_phone], $message, (string) Str::uuid(), 'result_sms', $request->user()->id);
                $sent++;
            } catch (SmsSendingException $e) {
                $failed++;
                break;
            }
        }

        return redirect()->route('school.sms-send.result.index')->with('status', "Sent: {$sent}".($failed ? ", stopped early: {$failed}" : ''));
    }

    private function classResults(int $examId, int $classId): Collection
    {
        $exam = Exam::with(['examSubjects.subject', 'examSubjects.marks'])->findOrFail($examId);
        $examSubjects = $exam->examSubjects->where('school_class_id', $classId);

        $students = Student::where('school_class_id', $classId)->where('status', 'active')->orderBy('roll')->get();

        return $students->map(function ($student) use ($examSubjects) {
            $totalFull = 0;
            $totalObtained = 0;

            foreach ($examSubjects as $examSubject) {
                $mark = $examSubject->marks->firstWhere('student_id', $student->id);
                $obtained = $mark && ! $mark->is_absent ? (float) $mark->marks_obtained : 0;

                $totalFull += $examSubject->full_marks;
                $totalObtained += $obtained;
            }

            $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
            $grade = Grade::forPercent($percentage);

            return [
                'student' => $student,
                'total_obtained' => $totalObtained,
                'total_full' => $totalFull,
                'percentage' => $percentage,
                'grade' => $grade?->name,
                'gpa' => $grade?->grade_point,
            ];
        });
    }
}
