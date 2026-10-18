<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolSmsCreditOrder;
use App\Models\SmsSendRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsReportController extends Controller
{
    public function dateWise(Request $request): View
    {
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $sendRequests = SmsSendRequest::whereBetween('created_at', [$from, $to])->latest()->paginate(20);

        return view('school.sms-reports.date-wise', compact('sendRequests', 'from', 'to'));
    }

    public function purchases(Request $request): View
    {
        $from = $request->date('from') ?? now()->subMonths(6);
        $to = $request->date('to') ?? now();

        $orders = SchoolSmsCreditOrder::whereBetween('created_at', [$from, $to])->latest()->paginate(20);

        return view('school.sms-reports.purchases', compact('orders', 'from', 'to'));
    }
}
