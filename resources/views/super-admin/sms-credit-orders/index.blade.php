<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('School SMS Credit Orders') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">School</th>
                            <th class="py-3 px-4">Credits</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Method</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Requested By</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $order->school->name ?? '-' }}</td>
                                <td class="py-3 px-4">{{ number_format($order->credits) }}</td>
                                <td class="py-3 px-4">{{ number_format($order->amount, 2) }}</td>
                                <td class="py-3 px-4">{{ $order->method }}</td>
                                <td class="py-3 px-4 capitalize">{{ $order->status }}</td>
                                <td class="py-3 px-4">{{ $order->requestedBy->name ?? '-' }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    @if ($order->status === 'pending')
                                        <form action="{{ route('super-admin.sms-credit-orders.approve', $order) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:underline">Approve</button>
                                        </form>
                                        <form action="{{ route('super-admin.sms-credit-orders.reject', $order) }}" method="POST" class="inline" onsubmit="return confirm('Reject this order?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:underline">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-gray-400">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-4 px-4 text-center text-gray-400">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
