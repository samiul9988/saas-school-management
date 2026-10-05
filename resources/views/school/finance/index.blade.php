<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Finance Overview') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Outstanding Dues</div>
                    <div class="text-3xl font-bold text-red-600">{{ number_format($stats['total_due'], 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Fees Collected (This Month)</div>
                    <div class="text-3xl font-bold text-green-600">{{ number_format($stats['collected_this_month'], 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Other Income (This Month)</div>
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['income_this_month'], 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Expenses (This Month)</div>
                    <div class="text-3xl font-bold text-orange-600">{{ number_format($stats['expense_this_month'], 2) }}</div>
                </div>
            </div>

            <div class="flex gap-4 text-sm">
                <a href="{{ route('school.fee-types.index') }}" class="text-indigo-600 hover:underline">Fee Types</a>
                <a href="{{ route('school.student-fees.index') }}" class="text-indigo-600 hover:underline">Student Fees</a>
                <a href="{{ route('school.incomes.index') }}" class="text-indigo-600 hover:underline">Income</a>
                <a href="{{ route('school.expenses.index') }}" class="text-indigo-600 hover:underline">Expenses</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4">Recent Fee Payments</h3>
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b text-gray-500">
                            <th class="py-2">Receipt</th>
                            <th class="py-2">Student</th>
                            <th class="py-2">Amount</th>
                            <th class="py-2">Method</th>
                            <th class="py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentPayments as $payment)
                            <tr class="border-b">
                                <td class="py-2">
                                    <a href="{{ route('school.fee-payments.receipt', $payment) }}" class="text-indigo-600 hover:underline">{{ $payment->receipt_number }}</a>
                                </td>
                                <td class="py-2">{{ $payment->student->name }}</td>
                                <td class="py-2">{{ number_format($payment->amount, 2) }}</td>
                                <td class="py-2 capitalize">{{ $payment->method }}</td>
                                <td class="py-2">{{ $payment->paid_on->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-400">No payments recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
