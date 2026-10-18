<?php

namespace App\Http\Controllers\School;

use App\Exceptions\Sms\SmsSendingException;
use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use App\Models\Student;
use App\Services\Sms\SmsSendingService;
use App\Services\Sms\SmsTemplateRenderer;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BirthdaySmsController extends Controller
{
    public function index(Request $request): View
    {
        $date = $request->filled('date') ? Carbon::parse($request->date('date')) : today();
        $students = Student::whereMonth('date_of_birth', $date->month)
            ->whereDay('date_of_birth', $date->day)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $defaultContent = SmsTemplate::defaultContentFor('birthday');

        return view('school.sms-send.birthday', compact('date', 'students', 'defaultContent'));
    }

    public function store(Request $request, SmsSendingService $sendingService, SmsTemplateRenderer $renderer): RedirectResponse
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $students = Student::whereIn('id', $data['student_ids'])->where('status', 'active')->whereNotNull('guardian_phone')->get();

        $sent = 0;
        $failed = 0;

        foreach ($students as $student) {
            $age = $student->date_of_birth ? $student->date_of_birth->diffInYears(today()) : null;

            $message = $renderer->render($data['content'], [
                'student_name' => $student->name,
                'age' => $age,
            ]);

            try {
                $sendingService->send($request->user()->school, [$student->guardian_phone], $message, (string) Str::uuid(), 'birthday_sms', $request->user()->id);
                $sent++;
            } catch (SmsSendingException $e) {
                $failed++;
                break;
            }
        }

        return redirect()->route('school.sms-send.birthday.index')->with('status', "Sent: {$sent}".($failed ? ", stopped early: {$failed}" : ''));
    }
}
