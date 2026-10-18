<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CentralSmsLedger;
use App\Models\SmsCreditPurchase;
use App\Models\SmsGatewaySetting;
use App\Services\Sms\CentralSmsLedgerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SmsCreditPurchaseController extends Controller
{
    public function index(): View
    {
        $settings = SmsGatewaySetting::current();
        $purchases = SmsCreditPurchase::with('purchasedBy')->latest('purchased_at')->paginate(15);
        $ledger = CentralSmsLedger::latest()->limit(20)->get();

        return view('super-admin.sms-credit-purchases.index', compact('settings', 'purchases', 'ledger'));
    }

    public function create(): View
    {
        return view('super-admin.sms-credit-purchases.create');
    }

    public function store(Request $request, CentralSmsLedgerService $ledgerService): RedirectResponse
    {
        $data = $request->validate([
            'credits' => ['required', 'integer', 'min:1'],
            'cost' => ['required', 'numeric', 'min:0'],
            'provider_reference' => ['nullable', 'string', 'max:255'],
            'purchased_at' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($data, $request, $ledgerService) {
            $purchase = SmsCreditPurchase::create([
                'credits' => $data['credits'],
                'cost' => $data['cost'],
                'provider_reference' => $data['provider_reference'] ?? null,
                'purchased_by' => $request->user()->id,
                'purchased_at' => $data['purchased_at'],
            ]);

            $settings = SmsGatewaySetting::query()->lockForUpdate()->firstOrCreate([]);

            $ledgerService->record(
                $settings,
                'purchase',
                $data['credits'],
                SmsCreditPurchase::class,
                $purchase->id,
                'Credits purchased from provider',
                $request->user()->id,
            );
        });

        return redirect()->route('super-admin.sms-credit-purchases.index')->with('status', 'Purchase recorded and central balance updated.');
    }
}
