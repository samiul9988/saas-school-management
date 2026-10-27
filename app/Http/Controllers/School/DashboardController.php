<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
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

        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_users' => User::where('school_id', $school->id)->count(),
            'sms_sent' => SmsSendRequest::where('status', 'sent')->count(),
            'sms_wallet_balance' => $wallets->walletFor($school)->balance,
            'present_today' => Attendance::where('date', today())->where('status', 'present')->count(),
            'absent_today' => Attendance::where('date', today())->where('status', 'absent')->count(),
        ];

        $currentYear = AcademicYear::where('is_current', true)->first() ?? AcademicYear::orderByDesc('starts_on')->first();

        $classSummary = SchoolClass::withCount('students')->orderBy('sort_order')->get();

        $recentStudents = Student::with('schoolClass', 'section')->latest()->limit(10)->get();

        return view('school.dashboard', [
            'school' => $school,
            'currentYear' => $currentYear,
            'stats' => $stats,
            'classSummary' => $classSummary,
            'recentStudents' => $recentStudents,
        ]);
    }
}
