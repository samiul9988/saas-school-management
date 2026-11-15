<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SmsSendRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\Sms\SchoolSmsWalletService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(SchoolSmsWalletService $wallets): View
    {
        $school = Auth::user()->school;

        // Only the Academic Year explicitly chosen on Institute Update should show here,
        // not whatever was most recently created via the quick-setup page.
        $currentYear = $school->academicYear;

        $studentsInYear = fn () => Student::query()
            ->when($currentYear, fn ($q) => $q->where('academic_year_id', $currentYear->id));

        $stats = [
            'total_students' => $studentsInYear()->count(),
            'total_teachers' => Teacher::count(),
            'total_users' => User::where('school_id', $school->id)->count(),
            'sms_sent' => SmsSendRequest::where('status', 'sent')->count(),
            'sms_wallet_balance' => $wallets->walletFor($school)->balance,
            'present_today' => Attendance::where('date', today())->where('status', 'present')->count(),
            'absent_today' => Attendance::where('date', today())->where('status', 'absent')->count(),
        ];

        $classSummary = SchoolClass::query()
            ->withCount(['students' => fn ($q) => $q->when($currentYear, fn ($q2) => $q2->where('academic_year_id', $currentYear->id))])
            ->orderBy('sort_order')
            ->get();

        $recentStudents = $studentsInYear()->with('schoolClass', 'section')->latest()->limit(10)->get();

        return view('school.dashboard', [
            'school' => $school,
            'currentYear' => $currentYear,
            'stats' => $stats,
            'classSummary' => $classSummary,
            'recentStudents' => $recentStudents,
        ]);
    }
}
