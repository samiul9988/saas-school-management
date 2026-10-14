<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use App\Models\StaffAttendanceTimeConfig;
use App\Models\Teacher;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffAttendanceController extends Controller
{
    public const WEEKDAYS = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

    public const LEAVE_REASONS = ['Casual Leave', 'Sick Leave', 'Earned Leave', 'Maternity Leave', 'Other'];

    private function activeTeachers()
    {
        return Teacher::where('status', 'active')->orderBy('staff_serial')->orderBy('name')->get();
    }

    public function takeAttendance(Request $request): View
    {
        $date = $request->get('date', now()->toDateString());
        $teachers = $this->activeTeachers();

        $existing = StaffAttendance::where('date', $date)
            ->whereIn('teacher_id', $teachers->pluck('id'))
            ->get()
            ->keyBy('teacher_id');

        $teachers->each(fn ($teacher) => $teacher->attendance = $existing->get($teacher->id));

        return view('school.hr-attendance.take-attendance', compact('date', 'teachers'));
    }

    public function takeAttendanceStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'absent_ids' => ['array'],
            'absent_ids.*' => ['exists:teachers,id'],
            'in_time' => ['array'],
            'out_time' => ['array'],
        ]);

        $absentIds = $data['absent_ids'] ?? [];
        $teachers = $this->activeTeachers();

        foreach ($teachers as $teacher) {
            $isAbsent = in_array($teacher->id, $absentIds);
            $in = $data['in_time'][$teacher->id] ?? null;
            $out = $data['out_time'][$teacher->id] ?? null;

            StaffAttendance::updateOrCreate(
                ['teacher_id' => $teacher->id, 'date' => $data['date']],
                [
                    'status' => $isAbsent ? 'absent' : 'present',
                    'in_time' => $isAbsent ? null : ($in ?: null),
                    'out_time' => $isAbsent ? null : ($out ?: null),
                ]
            );
        }

        return redirect()->route('school.hr-attendance.take-attendance', ['date' => $data['date']])
            ->with('status', 'Staff attendance saved.');
    }

    public function updateAttendance(Request $request): View
    {
        $date = $request->get('date');
        $records = collect();

        if ($date) {
            $records = StaffAttendance::with('teacher')
                ->where('date', $date)
                ->whereHas('teacher')
                ->get()
                ->sortBy(fn ($record) => $record->teacher?->name);
        }

        return view('school.hr-attendance.update-attendance', compact('date', 'records'));
    }

    public function updateAttendanceStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'absent_ids' => ['array'],
            'absent_ids.*' => ['exists:teachers,id'],
            'in_time' => ['array'],
            'out_time' => ['array'],
            'record_ids' => ['array'],
        ]);

        $absentIds = $data['absent_ids'] ?? [];

        foreach (($data['record_ids'] ?? []) as $teacherId) {
            $isAbsent = in_array($teacherId, $absentIds);
            $in = $data['in_time'][$teacherId] ?? null;
            $out = $data['out_time'][$teacherId] ?? null;

            StaffAttendance::updateOrCreate(
                ['teacher_id' => $teacherId, 'date' => $data['date']],
                [
                    'status' => $isAbsent ? 'absent' : 'present',
                    'in_time' => $isAbsent ? null : ($in ?: null),
                    'out_time' => $isAbsent ? null : ($out ?: null),
                ]
            );
        }

        return redirect()->route('school.hr-attendance.update-attendance', ['date' => $data['date']])
            ->with('status', 'Staff attendance updated.');
    }

    public function reportStaffInOutTime(Request $request): View
    {
        $weekStart = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfWeek(Carbon::SATURDAY)
            : now()->startOfWeek(Carbon::SATURDAY);

        $weekDates = collect(self::WEEKDAYS)->mapWithKeys(
            fn ($day, $index) => [$day => $weekStart->copy()->addDays($index)->toDateString()]
        );

        $teachers = $this->activeTeachers();

        $attendances = StaffAttendance::whereIn('teacher_id', $teachers->pluck('id'))
            ->whereIn('date', $weekDates->values())
            ->get()
            ->groupBy('teacher_id');

        $teachers->each(function ($teacher) use ($attendances, $weekDates) {
            $teacher->week = $weekDates->map(
                fn ($date) => $attendances->get($teacher->id, collect())->firstWhere('date', $date)
            );
        });

        return view('school.hr-attendance.report.staff-in-out-time', compact('teachers', 'weekStart'));
    }

    public function reportDateWise(Request $request): View
    {
        $date = $request->get('date');
        $records = collect();

        if ($date) {
            $records = StaffAttendance::with('teacher')
                ->where('date', $date)
                ->whereHas('teacher')
                ->get()
                ->sortBy(fn ($record) => $record->teacher?->name);
        }

        return view('school.hr-attendance.report.date-wise', compact('date', 'records'));
    }

    public function reportMonthWise(Request $request): View
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $teacherId = $request->get('teacher_id');
        $teachers = $this->activeTeachers();
        $teacher = null;
        $records = collect();

        if ($teacherId) {
            $teacher = Teacher::find($teacherId);
            if ($teacher) {
                $records = StaffAttendance::where('teacher_id', $teacher->id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->orderBy('date')
                    ->get();
            }
        }

        return view('school.hr-attendance.report.month-wise', compact('year', 'month', 'teachers', 'teacher', 'records'));
    }

    public function reportMonthWiseExcel(Request $request): StreamedResponse
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $teachers = $this->activeTeachers();
        $records = StaffAttendance::whereIn('teacher_id', $teachers->pluck('id'))
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->groupBy('teacher_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Staff Attendance');
        $sheet->fromArray(['ID', 'Name', 'Designation', 'Date', 'Status', 'In Time', 'Out Time'], null, 'A1');

        $row = 2;
        foreach ($teachers as $teacher) {
            foreach ($records->get($teacher->id, collect()) as $record) {
                $sheet->fromArray([
                    $teacher->staff_serial ?? $teacher->id,
                    $teacher->name,
                    $teacher->designation,
                    $record->date->toDateString(),
                    ucfirst($record->status),
                    $record->in_time,
                    $record->out_time,
                ], null, "A{$row}");
                $row++;
            }
        }

        $filename = "staff-attendance-{$year}-{$month}.xlsx";

        return new StreamedResponse(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function leaveInput(Request $request): View
    {
        $teacher = null;
        $leaves = collect();

        if ($request->filled('teacher_search')) {
            $teacher = Teacher::where('id', $request->teacher_search)
                ->orWhere('staff_serial', $request->teacher_search)
                ->first();

            if ($teacher) {
                $leaves = StaffAttendance::where('teacher_id', $teacher->id)
                    ->where('status', 'leave')
                    ->orderByDesc('date')
                    ->get();
            }
        }

        return view('school.hr-attendance.leave-input', [
            'teacher' => $teacher,
            'leaves' => $leaves,
            'reasons' => self::LEAVE_REASONS,
        ]);
    }

    public function leaveInputStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'leave_reason' => ['required', 'string'],
            'leave_for' => ['nullable', 'string'],
        ]);

        $period = CarbonPeriod::create($data['from_date'], $data['to_date']);

        foreach ($period as $date) {
            StaffAttendance::updateOrCreate(
                ['teacher_id' => $data['teacher_id'], 'date' => $date->toDateString()],
                [
                    'status' => 'leave',
                    'leave_reason' => $data['leave_reason'],
                    'leave_for' => $data['leave_for'] ?? null,
                ]
            );
        }

        return redirect()->route('school.hr-attendance.leave-input', ['teacher_search' => $data['teacher_id']])
            ->with('status', 'Leave recorded.');
    }

    public function leaveInputDestroy(StaffAttendance $leave): RedirectResponse
    {
        $teacherId = $leave->teacher_id;
        $leave->delete();

        return redirect()->route('school.hr-attendance.leave-input', ['teacher_search' => $teacherId])
            ->with('status', 'Leave removed.');
    }

    public function settingTimeConfiguration(Request $request): View
    {
        $teachers = $this->activeTeachers();
        $config = StaffAttendanceTimeConfig::where('school_id', Auth::user()->school_id)
            ->whereNull('teacher_id')
            ->first();

        return view('school.hr-attendance.setting.time-configuration', compact('teachers', 'config'));
    }

    public function settingTimeConfigurationStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'teacher_ids' => ['array'],
            'teacher_ids.*' => ['exists:teachers,id'],
            'in_time' => ['nullable'],
            'out_time' => ['nullable'],
            'delay_time' => ['nullable'],
            'input_process_time' => ['nullable'],
            'output_process_time' => ['nullable'],
            'days' => ['array'],
            'days.*' => ['in:' . implode(',', self::WEEKDAYS)],
        ]);

        $payload = [
            'in_time' => $data['in_time'] ?? null,
            'out_time' => $data['out_time'] ?? null,
            'delay_time' => $data['delay_time'] ?? null,
            'input_process_time' => $data['input_process_time'] ?? null,
            'output_process_time' => $data['output_process_time'] ?? null,
            'days' => $data['days'] ?? [],
        ];

        $teacherIds = $data['teacher_ids'] ?? [];

        if (empty($teacherIds)) {
            StaffAttendanceTimeConfig::updateOrCreate(
                ['school_id' => Auth::user()->school_id, 'teacher_id' => null],
                $payload
            );
        } else {
            foreach ($teacherIds as $teacherId) {
                StaffAttendanceTimeConfig::updateOrCreate(
                    ['school_id' => Auth::user()->school_id, 'teacher_id' => $teacherId],
                    $payload
                );
            }
        }

        return redirect()->route('school.hr-attendance.setting.time-configuration')
            ->with('status', 'Time configuration saved.');
    }

    public function settingMachineIdMapping(Request $request): View
    {
        $tab = $request->get('tab', 'enabled');
        $teachers = Teacher::where('status', $tab === 'disabled' ? 'inactive' : 'active')
            ->orderBy('staff_serial')
            ->orderBy('name')
            ->get();

        return view('school.hr-attendance.setting.machine-id-mapping', compact('tab', 'teachers'));
    }

    public function settingMachineIdMappingUpdate(Request $request, Teacher $teacher): RedirectResponse
    {
        $data = $request->validate(['machine_id' => ['nullable', 'string', 'max:255']]);
        $teacher->update(['machine_id' => $data['machine_id'] ?? null]);

        return back()->with('status', "Machine ID updated for {$teacher->name}.");
    }

    public function settingMachineIdMappingBatchUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'teacher_ids' => ['required', 'array'],
            'teacher_ids.*' => ['exists:teachers,id'],
            'machine_id' => ['array'],
        ]);

        foreach ($data['teacher_ids'] as $teacherId) {
            $teacher = Teacher::find($teacherId);
            if ($teacher) {
                $teacher->update(['machine_id' => $data['machine_id'][$teacherId] ?? null]);
            }
        }

        return back()->with('status', 'Machine IDs updated.');
    }
}
