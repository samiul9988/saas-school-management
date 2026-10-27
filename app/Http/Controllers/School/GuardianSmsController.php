<?php

namespace App\Http\Controllers\School;

use App\Exceptions\Sms\SmsSendingException;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SmsTemplate;
use App\Models\Student;
use App\Services\Sms\SmsSendingService;
use App\Services\Sms\SmsTemplateRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuardianSmsController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $sections = Section::with('schoolClass')->orderBy('name')->get();
        $students = collect();

        if ($request->filled('school_class_id')) {
            $students = Student::where('school_class_id', $request->integer('school_class_id'))->where('status', 'active')->orderByRoll()->get();
        } elseif ($request->filled('section_id')) {
            $students = Student::where('section_id', $request->integer('section_id'))->where('status', 'active')->orderByRoll()->get();
        }

        $defaultContent = SmsTemplate::defaultContentFor('guardian');

        return view('school.sms-send.guardian', compact('classes', 'sections', 'students', 'defaultContent'));
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
            $message = $renderer->render($data['content'], [
                'student_name' => $student->name,
                'roll' => $student->roll,
                'class' => $student->schoolClass?->name,
                'section' => $student->section?->name,
                'father_name' => $student->father_name,
                'guardian_name' => $student->guardian_name,
            ]);

            try {
                $sendingService->send($request->user()->school, [$student->guardian_phone], $message, (string) Str::uuid(), 'guardian_sms', $request->user()->id);
                $sent++;
            } catch (SmsSendingException $e) {
                $failed++;
                break;
            }
        }

        return redirect()->route('school.sms-send.guardian.index')->with('status', "Sent: {$sent}".($failed ? ", stopped early: {$failed}" : ''));
    }
}
