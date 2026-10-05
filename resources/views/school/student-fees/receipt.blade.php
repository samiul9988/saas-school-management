<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payment Receipt') }}</h2>
    </x-slot>

    <div class="py-12 print:py-0">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 print:hidden">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 print:shadow-none">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <div class="font-semibold text-lg">Receipt #{{ $feePayment->receipt_number }}</div>
                        <div class="text-sm text-gray-500">{{ $feePayment->paid_on->format('Y-m-d') }}</div>
                    </div>
                    <button onclick="window.print()" class="print:hidden text-indigo-600 hover:underline text-sm">Print</button>
                </div>

                <table class="w-full text-sm text-left">
                    <tbody>
                        <tr class="border-b"><td class="py-2 text-gray-500">Student</td><td class="py-2">{{ $feePayment->student->name }}</td></tr>
                        <tr class="border-b"><td class="py-2 text-gray-500">Fee Type</td><td class="py-2">{{ $feePayment->studentFee->feeType->name }}</td></tr>
                        <tr class="border-b"><td class="py-2 text-gray-500">Amount Paid</td><td class="py-2 font-semibold">{{ number_format($feePayment->amount, 2) }}</td></tr>
                        <tr class="border-b"><td class="py-2 text-gray-500">Method</td><td class="py-2 capitalize">{{ $feePayment->method }}</td></tr>
                        @if ($feePayment->transaction_id)
                            <tr class="border-b"><td class="py-2 text-gray-500">Transaction ID</td><td class="py-2">{{ $feePayment->transaction_id }}</td></tr>
                        @endif
                        <tr><td class="py-2 text-gray-500">Received By</td><td class="py-2">{{ $feePayment->receivedBy?->name ?? '—' }}</td></tr>
                    </tbody>
                </table>
            </div>

            <a href="{{ route('school.student-fees.index') }}" class="text-indigo-600 hover:underline print:hidden block mt-4">&larr; Back to student fees</a>
        </div>
    </div>
</x-app-layout>
