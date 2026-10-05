<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Fees') }} &mdash; {{ $student->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Fee Type</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Paid</th>
                            <th class="py-3 px-4">Due</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fees as $fee)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $fee->feeType->name }}</td>
                                <td class="py-3 px-4">{{ number_format($fee->amount, 2) }}</td>
                                <td class="py-3 px-4">{{ number_format($fee->paid_amount, 2) }}</td>
                                <td class="py-3 px-4">{{ number_format($fee->dueAmount(), 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="{{ $fee->status === 'paid' ? 'text-green-600' : ($fee->status === 'partial' ? 'text-yellow-600' : 'text-red-600') }} capitalize">{{ $fee->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
