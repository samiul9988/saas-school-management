<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Period;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceReportController extends Controller
{
    private function sections()
    {
        return Section::with('schoolClass', 'shift')->orderBy('serial')->orderBy('name')->get();
    }

    private function periods()
    {
        return Period::orderBy('serial')->get();
    }

    public function allSectionSummary(Request $request): View
    {
        $periods = $this->periods();
        $date = $request->get('date', now()->toDateString());
        $periodId = $request->get('period_id') ?: null;
        $rows = collect();

        if ($request->filled('date')) {
            $rows = $this->sections()->map(function ($section) use ($date, $periodId) {
                $total = Student::where('section_id', $section->id)->where('status', 'active')->count();
                $present = Attendance::where('section_id', $section->id)->where('date', $date)->where('period_id', $periodId)->where('status', 'present')->count();
                $absent = Attendance::where('section_id', $section->id)->where('date', $date)->where('period_id', $periodId)->where('status', 'absent')->count();

                return [
                    'class' => $section->schoolClass?->name,
                    'shift' => $section->shift?->name ?? 'N/A',
                    'section' => $section->name,
                    'total' => $total,
                    'present' => $present,
                    'absent' => $absent,
                ];
            });
        }

        return view('school.attendance.report.all-section-summary', compact('periods', 'date', 'periodId', 'rows'));
    }

    public function sectionDetailsDay(Request $request): View
    {
        $sections = $this->sections();
        $periods = $this->periods();
        $date = $request->get('date', now()->toDateString());
        $section = null;
        $rows = collect();

        if ($request->filled('section_id')) {
            $section = Section::findOrFail($request->section_id);
            $periodId = $request->get('period_id') ?: null;

            $students = Student::where('section_id', $section->id)->where('status', 'active')->orderBy('roll')->get();
            $attendances = Attendance::where('date', $date)->where('period_id', $periodId)
                ->whereIn('student_id', $students->pluck('id'))->pluck('status', 'student_id');

            $rows = $students->map(fn ($s) => [
                'id' => $s->admission_number, 'name' => $s->name, 'roll' => $s->roll,
                'status' => $attendances->get($s->id, '-'),
            ]);
        }

        return view('school.attendance.report.section-details-day', compact('sections', 'periods', 'date', 'section', 'rows'));
    }

    public function sectionDetailsMonth(Request $request): View
    {
        $sections = $this->sections();
        $periods = $this->periods();
        $years = range(now()->year - 2, now()->year + 1);
        $section = null;
        $rows = collect();
        $daysInMonth = 0;

        if ($request->filled(['section_id', 'year', 'month'])) {
            $section = Section::findOrFail($request->section_id);
            $periodId = $request->get('period_id') ?: null;
            $year = (int) $request->year;
            $month = (int) $request->month;
            $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;

            $students = Student::where('section_id', $section->id)->where('status', 'active')->orderBy('roll')->get();
            $attendances = Attendance::where('period_id', $periodId)
                ->whereIn('student_id', $students->pluck('id'))
                ->whereYear('date', $year)->whereMonth('date', $month)
                ->get()
                ->groupBy('student_id');

            $rows = $students->map(function ($s) use ($attendances) {
                $byDay = ($attendances->get($s->id) ?? collect())->keyBy(fn ($a) => $a->date->day);

                return ['id' => $s->admission_number, 'name' => $s->name, 'roll' => $s->roll, 'byDay' => $byDay];
            });
        }

        return view('school.attendance.report.section-details-month', compact(
            'sections', 'periods', 'years', 'section', 'rows', 'daysInMonth'
        ));
    }

    public function monthlySummary(Request $request): View
    {
        $sections = $this->sections();
        $periods = $this->periods();
        $years = range(now()->year - 2, now()->year + 1);
        $section = null;
        $rows = collect();

        if ($request->filled(['section_id', 'year', 'month'])) {
            $section = Section::findOrFail($request->section_id);
            $periodId = $request->get('period_id') ?: null;
            $year = (int) $request->year;
            $month = (int) $request->month;

            $students = Student::where('section_id', $section->id)->where('status', 'active')->orderBy('roll')->get();
            $attendances = Attendance::where('period_id', $periodId)
                ->whereIn('student_id', $students->pluck('id'))
                ->whereYear('date', $year)->whereMonth('date', $month)
                ->get()
                ->groupBy('student_id');

            $rows = $students->map(function ($s) use ($attendances) {
                $records = $attendances->get($s->id) ?? collect();

                return [
                    'id' => $s->admission_number, 'name' => $s->name, 'roll' => $s->roll,
                    'present' => $records->where('status', 'present')->count(),
                    'absent' => $records->where('status', 'absent')->count(),
                    'leave' => $records->where('status', 'leave')->count(),
                    'late' => $records->where('status', 'late')->count(),
                ];
            });
        }

        return view('school.attendance.report.monthly-summary', compact('sections', 'periods', 'years', 'section', 'rows'));
    }

    public function deviceData(Request $request): View
    {
        $date = $request->get('date', now()->toDateString());

        // No biometric/attendance-device integration exists in this system yet,
        // so this report has no real data source to query — matches the
        // reference product's own empty state for schools without a device.
        $rows = collect();

        return view('school.attendance.report.device-data', compact('date', 'rows'));
    }
}
