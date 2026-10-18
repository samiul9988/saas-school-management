<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSmsCreditOrder;
use App\Services\Sms\SchoolSmsWalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolSmsCreditOrderController extends Controller
{
    public function index(): View
    {
        $orders = SchoolSmsCreditOrder::withoutGlobalScope('school')
            ->with('school', 'requestedBy')
            ->latest()
            ->paginate(20);

        return view('super-admin.sms-credit-orders.index', compact('orders'));
    }

    public function approve(SchoolSmsCreditOrder $order, SchoolSmsWalletService $wallets, Request $request): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return redirect()->route('super-admin.sms-credit-orders.index')->with('status', 'This order has already been processed.');
        }

        DB::transaction(function () use ($order, $wallets, $request) {
            $order->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            $wallets->creditFromApprovedOrder(
                $order->school,
                $order->credits,
                SchoolSmsCreditOrder::class,
                $order->id,
                $request->user()->id,
            );
        });

        return redirect()->route('super-admin.sms-credit-orders.index')->with('status', 'Order approved and school wallet credited.');
    }

    public function reject(SchoolSmsCreditOrder $order, Request $request): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return redirect()->route('super-admin.sms-credit-orders.index')->with('status', 'This order has already been processed.');
        }

        $order->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('super-admin.sms-credit-orders.index')->with('status', 'Order rejected.');
    }
}
