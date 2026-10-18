<?php

namespace App\Http\Controllers\School;

use App\Exceptions\Sms\SmsSendingException;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Shift;
use App\Models\SmsTemplate;
use App\Models\StudentFee;
use App\Services\Sms\SmsSendingService;
use App\Services\Sms\SmsTemplateRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DueFeeSmsController extends Controller
{
    public function index(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $shifts = Shift::orderBy('name')->get();
        $dueStudents = collect();

        if ($request->filled('academic_year_id') && $request->filled('shift_id')) {
            $dueStudents = $this->dueStudents($request->integer('academic_year_id'), $request->integer('shift_id'));
        }

        $defaultContent = SmsTemplate::defaultContentFor('due_fee');

        return view('school.sms-send.due-fee', compact('academicYears', 'shifts', 'dueStudents', 'defaultContent'));
    }

    public function store(Request $request, SmsSendingService $sendingService, SmsTemplateRenderer $renderer): RedirectResponse
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
            'academic_year_id' => ['required', 'integer'],
            'shift_id' => ['required', 'integer'],
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $dueStudents = $this->dueStudents($data['academic_year_id'], $data['shift_id'])
            ->whereIn('id', $data['student_ids']);

        $sent = 0;
        $failed = 0;

        foreach ($dueStudents as $student) {
            if (! $student->guardian_phone) {
                continue;
            }

            $message = $renderer->render($data['content'], [
                'student_name' => $student->name,
                'class' => $student->schoolClass?->name,
                'due_amount' => number_format($student->due_amount, 2),
            ]);

            try {
                $sendingService->send($request->user()->school, [$student->guardian_phone], $message, (string) Str::uuid(), 'due_fee_sms', $request->user()->id);
                $sent++;
            } catch (SmsSendingException $e) {
                $failed++;
                break;
            }
        }

        return redirect()->route('school.sms-send.due-fee.index')->with('status', "Sent: {$sent}".($failed ? ", stopped early: {$failed}" : ''));
    }

    private function dueStudents(int $academicYearId, int $shiftId): Collection
    {
        $fees = StudentFee::where('academic_year_id', $academicYearId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereHas('student.section', fn ($q) => $q->where('shift_id', $shiftId))
            ->with('student.schoolClass')
            ->get()
            ->groupBy('student_id');

        return $fees->map(function ($studentFees) {
            $student = $studentFees->first()->student;
            $student->due_amount = $studentFees->sum(fn ($fee) => $fee->dueAmount());

            return $student;
        })->values();
    }
}
