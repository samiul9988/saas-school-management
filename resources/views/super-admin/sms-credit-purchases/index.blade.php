<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Provider SMS Purchases') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex items-center justify-between">
                <p class="text-lg">Central Balance: <span class="font-bold">{{ number_format($settings->central_balance) }}</span> credits</p>
                <a href="{{ route('super-admin.sms-credit-purchases.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Record Purchase</a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Credits</th>
                            <th class="py-3 px-4">Cost</th>
                            <th class="py-3 px-4">Provider Ref</th>
                            <th class="py-3 px-4">Purchased By</th>
                            <th class="py-3 px-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $purchase)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ number_format($purchase->credits) }}</td>
                                <td class="py-3 px-4">{{ number_format($purchase->cost, 2) }}</td>
                                <td class="py-3 px-4">{{ $purchase->provider_reference ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $purchase->purchasedBy->name ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $purchase->purchased_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 px-4 text-center text-gray-400">No purchases recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $purchases->links() }}</div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Recent Central Ledger Activity</h3>
                <table class="w-full text-sm text-left">
                    <thead><tr class="text-gray-500 border-b"><th class="py-2">Type</th><th class="py-2">Credits</th><th class="py-2">Balance After</th><th class="py-2">Note</th><th class="py-2">Date</th></tr></thead>
                    <tbody>
                        @forelse ($ledger as $entry)
                            <tr class="border-b">
                                <td class="py-2 capitalize">{{ $entry->type }}</td>
                                <td class="py-2 {{ $entry->credits < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $entry->credits > 0 ? '+' : '' }}{{ $entry->credits }}</td>
                                <td class="py-2">{{ $entry->balance_after }}</td>
                                <td class="py-2 text-gray-500">{{ $entry->note }}</td>
                                <td class="py-2">{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-400">No ledger activity yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
