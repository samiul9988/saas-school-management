<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SubscriptionPayment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_schools' => School::count(),
            'active_schools' => School::where('status', 'active')->count(),
            'trial_schools' => School::where('status', 'trial')->count(),
            'expired_schools' => School::where('status', 'expired')->count(),
            'monthly_revenue' => SubscriptionPayment::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'pending_payments' => SubscriptionPayment::where('status', 'pending')->count(),
        ];

        $recentSchools = School::latest()->limit(10)->get();

        return view('super-admin.dashboard', compact('stats', 'recentSchools'));
    }
}
