<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SmsSendRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsUsageReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $query = SmsSendRequest::withoutGlobalScope('school')->whereBetween('created_at', [$from, $to]);

        $summary = [
            'total' => (clone $query)->count(),
            'sent' => (clone $query)->where('status', 'sent')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'unknown' => (clone $query)->where('status', 'unknown')->count(),
            'credits_consumed' => (clone $query)->where('status', 'sent')->sum('credits_required'),
        ];

        $bySchool = (clone $query)
            ->selectRaw('school_id, status, count(*) as total, sum(credits_required) as credits')
            ->groupBy('school_id', 'status')
            ->with('school')
            ->get()
            ->groupBy('school_id');

        return view('super-admin.sms-usage-report.index', compact('summary', 'bySchool', 'from', 'to'));
    }
}
