<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'present_today' => Attendance::where('date', today())->where('status', 'present')->count(),
            'absent_today' => Attendance::where('date', today())->where('status', 'absent')->count(),
        ];

        $recentStudents = Student::with('schoolClass', 'section')->latest()->limit(10)->get();

        return view('school.dashboard', compact('stats', 'recentStudents'));
    }
}
