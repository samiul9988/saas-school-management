<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolSmsCreditOrder;
use App\Models\SmsGatewaySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsCreditOrderController extends Controller
{
    public function create(): View
    {
        $pricePerSms = SmsGatewaySetting::current()->price_per_sms;

        return view('school.sms-credit-orders.create', compact('pricePerSms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'credits' => ['required', 'integer', 'min:1', 'max:1000000'],
            'method' => ['required', 'in:manual,bank,mobile_banking,other'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
        ]);

        // Price is always derived server-side; never trusted from client input.
        $pricePerSms = SmsGatewaySetting::current()->price_per_sms;

        SchoolSmsCreditOrder::create([
            'school_id' => $request->user()->school_id,
            'credits' => $data['credits'],
            'price_per_sms' => $pricePerSms,
            'amount' => round($pricePerSms * $data['credits'], 2),
            'method' => $data['method'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'status' => 'pending',
            'requested_by' => $request->user()->id,
        ]);

        return redirect()->route('school.sms-wallet.index')->with('status', 'Credit purchase request submitted. It will be reviewed by the super admin.');
    }
}
