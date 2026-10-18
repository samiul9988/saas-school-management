<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolSmsCreditOrder;
use App\Models\SchoolSmsWalletTransaction;
use App\Models\SmsSendRequest;
use App\Services\Sms\SchoolSmsWalletService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsWalletController extends Controller
{
    public function index(Request $request, SchoolSmsWalletService $wallets): View
    {
        $school = $request->user()->school;
        $wallet = $wallets->walletFor($school);

        $transactions = SchoolSmsWalletTransaction::latest()->paginate(15, pageName: 'transactions');
        $sendRequests = SmsSendRequest::latest()->limit(15)->get();
        $orders = SchoolSmsCreditOrder::latest()->limit(10)->get();

        return view('school.sms-wallet.index', compact('wallet', 'transactions', 'sendRequests', 'orders'));
    }
}
