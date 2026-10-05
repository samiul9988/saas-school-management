<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Fees') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-between items-center">
                <form method="GET" class="flex gap-2">
                    <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        <option value="unpaid" @selected(request('status') === 'unpaid')>Unpaid</option>
                        <option value="partial" @selected(request('status') === 'partial')>Partial</option>
                        <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                    </select>
                </form>
                <a href="{{ route('school.student-fees.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Assign Fee
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Student</th>
                            <th class="py-3 px-4">Fee Type</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Paid</th>
                            <th class="py-3 px-4">Due</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fees as $fee)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $fee->student->name }}</td>
                                <td class="py-3 px-4">{{ $fee->feeType->name }}</td>
                                <td class="py-3 px-4">{{ number_format($fee->amount, 2) }}</td>
                                <td class="py-3 px-4">{{ number_format($fee->paid_amount, 2) }}</td>
                                <td class="py-3 px-4">{{ number_format($fee->dueAmount(), 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="{{ $fee->status === 'paid' ? 'text-green-600' : ($fee->status === 'partial' ? 'text-yellow-600' : 'text-red-600') }} capitalize">{{ $fee->status }}</span>
                                </td>
                                <td class="py-3 px-4 space-x-2">
                                    @if ($fee->status !== 'paid')
                                        <a href="{{ route('school.fee-payments.create', $fee) }}" class="text-indigo-600 hover:underline">Collect Payment</a>
                                    @endif
                                    <form action="{{ route('school.student-fees.destroy', $fee) }}" method="POST" class="inline" onsubmit="return confirm('Remove this fee assignment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $fees->links() }}
        </div>
    </div>
</x-app-layout>
