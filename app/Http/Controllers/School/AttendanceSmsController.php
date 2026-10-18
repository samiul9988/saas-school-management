<?php

namespace App\Http\Controllers\School;

use App\Exceptions\Sms\SmsSendingException;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Period;
use App\Models\Section;
use App\Models\SmsTemplate;
use App\Services\Sms\SmsSendingService;
use App\Services\Sms\SmsTemplateRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceSmsController extends Controller
{
    public function index(Request $request): View
    {
        $sections = Section::orderBy('name')->get();
        $periods = Period::orderBy('serial')->get();
        $records = collect();
        $summary = ['total' => 0, 'present' => 0, 'absent' => 0];

        if ($request->filled(['date', 'section_id', 'period_id'])) {
            $records = $this->attendanceRecords($request->date('date'), $request->integer('section_id'), $request->integer('period_id'));
            $summary = [
                'total' => $records->count(),
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
            ];
        }

        $defaultContent = SmsTemplate::defaultContentFor('attendance');

        return view('school.sms-send.attendance', compact('sections', 'periods', 'records', 'summary', 'defaultContent'));
    }

    public function store(Request $request, SmsSendingService $sendingService, SmsTemplateRenderer $renderer): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'section_id' => ['required', 'integer'],
            'period_id' => ['required', 'integer'],
            'target' => ['required', 'in:both,present,absent'],
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $records = $this->attendanceRecords($data['date'], $data['section_id'], $data['period_id']);

        if ($data['target'] !== 'both') {
            $records = $records->where('status', $data['target']);
        }

        $sent = 0;
        $failed = 0;

        foreach ($records as $record) {
            $student = $record->student;

            if (! $student || ! $student->guardian_phone) {
                continue;
            }

            $message = $renderer->render($data['content'], [
                'student_name' => $student->name,
                'status' => $record->status,
                'date' => $data['date'],
            ]);

            try {
                $sendingService->send($request->user()->school, [$student->guardian_phone], $message, (string) Str::uuid(), 'attendance_sms', $request->user()->id);
                $sent++;
            } catch (SmsSendingException $e) {
                $failed++;
                break;
            }
        }

        return redirect()->route('school.sms-send.attendance.index')->with('status', "Sent: {$sent}".($failed ? ", stopped early: {$failed}" : ''));
    }

    private function attendanceRecords($date, int $sectionId, int $periodId): Collection
    {
        return Attendance::where('date', $date)
            ->where('section_id', $sectionId)
            ->where('period_id', $periodId)
            ->with('student')
            ->get();
    }
}
