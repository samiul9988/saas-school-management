<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSmsWalletTransaction;
use App\Models\SmsSendRequest;
use App\Services\Sms\SchoolSmsWalletService;
use Illuminate\View\View;

class SmsSchoolOverviewController extends Controller
{
    public function index(SchoolSmsWalletService $wallets): View
    {
        $schools = School::with('smsWallet')->orderBy('name')->get()
            ->map(function (School $school) use ($wallets) {
                $school->sms_wallet_balance = $wallets->walletFor($school)->balance;

                return $school;
            });

        return view('super-admin.sms-schools.index', compact('schools'));
    }

    public function show(School $school, SchoolSmsWalletService $wallets): View
    {
        $wallet = $wallets->walletFor($school);
        $transactions = SchoolSmsWalletTransaction::withoutGlobalScope('school')
            ->where('school_id', $school->id)
            ->latest()
            ->paginate(20);
        $sendRequests = SmsSendRequest::withoutGlobalScope('school')
            ->where('school_id', $school->id)
            ->latest()
            ->limit(20)
            ->get();

        return view('super-admin.sms-schools.show', compact('school', 'wallet', 'transactions', 'sendRequests'));
    }
}
