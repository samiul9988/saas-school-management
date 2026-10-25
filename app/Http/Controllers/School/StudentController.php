<?php

namespace App\Http\Controllers\School;

use App\Exceptions\Sms\SmsSendingException;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\SmsTemplate;
use App\Models\Student;
use App\Services\Sms\SmsSendingService;
use App\Services\Sms\SmsTemplateRenderer;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentController extends Controller
{
    private const DEFAULT_WELCOME_SMS = 'Welcome to {{school_name}}! Admission confirmed for {{student_name}} in Class {{class}}. ID: {{student_id}}. Class starts on {{date}}. Thank you.';

    public function index(Request $request): View
    {
        $students = Student::with('schoolClass', 'section')
            ->when($request->filled('school_class_id'), fn ($q) => $q->where('school_class_id', $request->school_class_id))
            ->orderBy('roll')
            ->paginate(20)
            ->withQueryString();

        $classes = SchoolClass::orderBy('sort_order')->get();

        return view('school.students.index', compact('students', 'classes'));
    }

    public function create(): View
    {
        $classes = SchoolClass::with('sections')->orderBy('sort_order')->get();
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $groups = $this->configuredGroups();
        $defaultSmsContent = SmsTemplate::defaultContentFor('admission') ?: self::DEFAULT_WELCOME_SMS;

        return view('school.students.create', compact('classes', 'academicYears', 'groups', 'defaultSmsContent'));
    }

    public function store(Request $request, SmsSendingService $sendingService, SmsTemplateRenderer $renderer): RedirectResponse
    {
        $student = Student::create($this->validated($request));

        $smsStatus = null;

        if ($request->boolean('send_welcome_sms') && $student->guardian_phone) {
            $content = $request->input('welcome_sms_content') ?: (SmsTemplate::defaultContentFor('admission') ?: self::DEFAULT_WELCOME_SMS);

            $message = $renderer->render($content, [
                'school_name' => $request->user()->school->name,
                'student_name' => $student->name,
                'class' => $student->schoolClass?->name,
                'student_id' => $student->admission_number ?: $student->id,
                'date' => now()->format('Y-m-d'),
            ]);

            try {
                $sendingService->send($request->user()->school, [$student->guardian_phone], $message, (string) Str::uuid(), 'admission_sms', $request->user()->id);
            } catch (SmsSendingException $e) {
                $smsStatus = $e->getMessage();
            }
        }

        return redirect()->route('school.students.index')
            ->with('status', 'Student admitted.'.($smsStatus ? ' SMS not sent: '.$smsStatus : ''));
    }

    public function show(Student $student): View
    {
        $student->load('schoolClass', 'section', 'academicYear', 'attendances', 'portalUser');

        return view('school.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $classes = SchoolClass::with('sections')->orderBy('sort_order')->get();
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();
        $groups = $this->configuredGroups();

        return view('school.students.edit', compact('student', 'classes', 'academicYears', 'groups'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->update($this->validated($request));

        return redirect()->route('school.students.index')->with('status', 'Student updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        try {
            $student->delete();
        } catch (QueryException $e) {
            return redirect()->route('school.students.index')
                ->with('status', 'Cannot remove this student: related records (fees, attendance, results, etc.) still reference them. Disable the student instead.');
        }

        return redirect()->route('school.students.index')->with('status', 'Student removed.');
    }

    private function configuredGroups(): array
    {
        $groups = Group::where('status', 'enable')->orderBy('serial')->pluck('name')->all();

        return $groups ?: ['Science', 'Commerce', 'Arts', 'General'];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'admission_number' => ['nullable', 'string', 'max:255'],
            'roll' => ['nullable', 'string', 'max:50'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'religion' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'village' => ['nullable', 'string', 'max:255'],
            'post_office' => ['nullable', 'string', 'max:255'],
            'upazila_thana' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,transferred,graduated'],
        ]);
    }
}
