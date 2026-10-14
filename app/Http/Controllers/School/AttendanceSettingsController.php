<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceTimeConfig;
use App\Models\Period;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceSettingsController extends Controller
{
    public const GLOBAL_PERIODS = [
        '1st Period', '2nd Period', '3rd Period', '4th Period', '5th Period',
        '6th Period', '7th Period', '8th Period', '9th Period', '10th Period',
    ];

    public function periodSetup(): View
    {
        $periods = Period::orderBy('serial')->orderBy('id')->get();
        $addedNames = $periods->pluck('name')->all();

        return view('school.attendance.settings.period', compact('periods', 'addedNames'));
    }

    public function periodSetupStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'in:'.implode(',', self::GLOBAL_PERIODS)],
        ]);

        $nextSerial = (Period::max('serial') ?? -1) + 1;

        Period::firstOrCreate(['name' => $data['name']], ['serial' => $nextSerial, 'status' => 'enable']);

        return redirect()->route('school.student-attendance.settings.period')->with('status', 'Period added.');
    }

    public function timeConfig(Request $request): View
    {
        $sections = Section::with('schoolClass', 'shift')->orderBy('serial')->orderBy('name')->get();
        $periods = Period::orderBy('serial')->get();
        $configs = AttendanceTimeConfig::with('schoolClass', 'section.shift', 'period')->orderByDesc('id')->get();

        return view('school.attendance.settings.time-config', compact('sections', 'periods', 'configs'));
    }

    public function timeConfigStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'period_id' => ['required', 'exists:periods,id'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'delay_time' => ['nullable'],
            'process_time' => ['nullable'],
            'send_sms_status' => ['required', 'in:present,absent'],
        ]);

        $section = Section::findOrFail($data['section_id']);

        AttendanceTimeConfig::updateOrCreate(
            ['section_id' => $section->id, 'period_id' => $data['period_id']],
            [
                'school_class_id' => $section->school_class_id,
                'start_time' => $data['start_time'] ?: null,
                'end_time' => $data['end_time'] ?: null,
                'delay_time' => $data['delay_time'] ?: null,
                'process_time' => $data['process_time'] ?: null,
                'send_sms_status' => $data['send_sms_status'],
            ]
        );

        return redirect()->route('school.student-attendance.settings.time-config')->with('status', 'Attendance time configuration saved.');
    }

    public function manualProcess(): View
    {
        $school = Auth::user()->school;

        return view('school.attendance.settings.manual-process', compact('school'));
    }

    public function manualProcessStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        $students = Student::where('status', 'active')->get();
        $period = \Carbon\CarbonPeriod::create($data['from_date'], $data['to_date']);
        $created = 0;

        foreach ($period as $date) {
            $existing = Attendance::where('date', $date->toDateString())
                ->whereIn('student_id', $students->pluck('id'))
                ->pluck('student_id')
                ->all();

            foreach ($students as $student) {
                if (in_array($student->id, $existing)) {
                    continue;
                }

                Attendance::create([
                    'student_id' => $student->id,
                    'school_class_id' => $student->school_class_id,
                    'section_id' => $student->section_id,
                    'period_id' => null,
                    'date' => $date->toDateString(),
                    'status' => 'present',
                ]);
                $created++;
            }
        }

        return redirect()->route('school.student-attendance.settings.manual-process')
            ->with('status', "Processed. {$created} attendance record(s) defaulted to present for students with no entry in this range.");
    }
}
