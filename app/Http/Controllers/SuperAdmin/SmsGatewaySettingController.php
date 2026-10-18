<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SmsGatewaySetting;
use App\Services\Sms\SmsGatewayAdapterInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsGatewaySettingController extends Controller
{
    public function edit(): View
    {
        $settings = SmsGatewaySetting::current();

        return view('super-admin.sms-gateway.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string', 'max:255'],
            'api_key' => ['nullable', 'string', 'max:500'],
            'sender_id' => ['nullable', 'string', 'max:255'],
            'api_base_url' => ['nullable', 'url', 'max:255'],
            'price_per_sms' => ['required', 'numeric', 'min:0'],
            'low_balance_threshold' => ['nullable', 'integer', 'min:0'],
        ]);

        $settings = SmsGatewaySetting::current();

        if (empty($data['api_key'])) {
            unset($data['api_key']);
        }

        $settings->update($data);

        return redirect()->route('super-admin.sms-gateway.edit')->with('status', 'SMS gateway settings updated.');
    }

    public function syncBalance(SmsGatewayAdapterInterface $gateway): RedirectResponse
    {
        $providerBalance = $gateway->getBalance();

        if ($providerBalance === null) {
            return redirect()->route('super-admin.sms-gateway.edit')
                ->with('status', 'This provider does not support balance lookup, or the gateway is not configured.');
        }

        return redirect()->route('super-admin.sms-gateway.edit')
            ->with('status', "Provider reports a balance of {$providerBalance} credits. Internal ledger balance: ".SmsGatewaySetting::current()->central_balance.'.');
    }
}
