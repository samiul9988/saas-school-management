<?php

namespace App\Http\Controllers\School;

use App\Exceptions\Sms\SmsSendingException;
use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use App\Models\Teacher;
use App\Services\Sms\SmsSendingService;
use App\Services\Sms\SmsTemplateRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HrSmsController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::where('status', 'active')->whereNotNull('phone')->orderBy('name')->get();
        $defaultContent = SmsTemplate::defaultContentFor('hr');

        return view('school.sms-send.hr', compact('teachers', 'defaultContent'));
    }

    public function store(Request $request, SmsSendingService $sendingService, SmsTemplateRenderer $renderer): RedirectResponse
    {
        $data = $request->validate([
            'teacher_ids' => ['required', 'array', 'min:1'],
            'teacher_ids.*' => ['integer', 'exists:teachers,id'],
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $teachers = Teacher::whereIn('id', $data['teacher_ids'])->where('status', 'active')->whereNotNull('phone')->get();

        $sent = 0;
        $failed = 0;

        foreach ($teachers as $teacher) {
            $message = $renderer->render($data['content'], [
                'teacher_name' => $teacher->name,
                'designation' => $teacher->designation,
            ]);

            try {
                $sendingService->send($request->user()->school, [$teacher->phone], $message, (string) Str::uuid(), 'hr_sms', $request->user()->id);
                $sent++;
            } catch (SmsSendingException $e) {
                $failed++;
                break;
            }
        }

        return redirect()->route('school.sms-send.hr.index')->with('status', "Sent: {$sent}".($failed ? ", stopped early: {$failed}" : ''));
    }
}
