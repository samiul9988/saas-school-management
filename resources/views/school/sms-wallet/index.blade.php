<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('SMS Wallet') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Available Balance</p>
                    <p class="text-3xl font-bold">{{ number_format($wallet->balance) }} <span class="text-base font-normal text-gray-500">credits</span></p>
                    <p class="text-xs text-gray-400 mt-1">Purchased: {{ number_format($wallet->total_purchased) }} &middot; Used: {{ number_format($wallet->total_used) }}</p>
                </div>
                <a href="{{ route('school.sms-credit-orders.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Buy Credits
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Recent SMS Sends</h3>
                <table class="w-full text-sm text-left">
                    <thead><tr class="text-gray-500 border-b"><th class="py-2">Recipients</th><th class="py-2">Message</th><th class="py-2">Segments</th><th class="py-2">Credits</th><th class="py-2">Status</th><th class="py-2">Sent</th></tr></thead>
                    <tbody>
                        @forelse ($sendRequests as $req)
                            <tr class="border-b">
                                <td class="py-2">{{ count($req->recipients) }}</td>
                                <td class="py-2 truncate max-w-xs">{{ $req->message }}</td>
                                <td class="py-2">{{ $req->segments }}</td>
                                <td class="py-2">{{ $req->credits_required }}</td>
                                <td class="py-2 capitalize">{{ $req->status }}</td>
                                <td class="py-2">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-gray-400">No SMS sent yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Wallet Transactions</h3>
                <table class="w-full text-sm text-left">
                    <thead><tr class="text-gray-500 border-b"><th class="py-2">Type</th><th class="py-2">Credits</th><th class="py-2">Balance After</th><th class="py-2">Note</th><th class="py-2">Date</th></tr></thead>
                    <tbody>
                        @forelse ($transactions as $tx)
                            <tr class="border-b">
                                <td class="py-2 capitalize">{{ $tx->type }}</td>
                                <td class="py-2 {{ $tx->credits < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $tx->credits > 0 ? '+' : '' }}{{ $tx->credits }}</td>
                                <td class="py-2">{{ $tx->balance_after }}</td>
                                <td class="py-2 text-gray-500">{{ $tx->note }}</td>
                                <td class="py-2">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-400">No transactions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $transactions->links() }}</div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Credit Purchase Requests</h3>
                <table class="w-full text-sm text-left">
                    <thead><tr class="text-gray-500 border-b"><th class="py-2">Credits</th><th class="py-2">Amount</th><th class="py-2">Status</th><th class="py-2">Requested</th></tr></thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="border-b">
                                <td class="py-2">{{ number_format($order->credits) }}</td>
                                <td class="py-2">{{ number_format($order->amount, 2) }}</td>
                                <td class="py-2 capitalize">{{ $order->status }}</td>
                                <td class="py-2">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-400">No purchase requests yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
