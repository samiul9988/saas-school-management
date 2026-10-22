<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Monthly Payroll') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap gap-4 items-end justify-between">
                    <form method="GET" class="flex gap-2 items-end">
                        <div>
                            <x-input-label for="month" value="Month" />
                            <select id="month" name="month" class="mt-1 border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected($month === $m)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <x-input-label for="year" value="Year" />
                            <x-text-input id="year" type="number" name="year" class="mt-1 w-24" value="{{ $year }}" onchange="this.form.submit()" />
                        </div>
                    </form>
                    <form method="POST" action="{{ route('school.payrolls.generate') }}">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <x-primary-button>Generate Payroll</x-primary-button>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Teacher</th>
                            <th class="py-3 px-4">Net Salary</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payrolls as $payroll)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $payroll->teacher->name }}</td>
                                <td class="py-3 px-4">{{ number_format($payroll->net_salary, 2) }}</td>
                                <td class="py-3 px-4 capitalize">{{ $payroll->status }}</td>
                                <td class="py-3 px-4">
                                    @if ($payroll->status === 'pending')
                                        <form action="{{ route('school.payrolls.mark-paid', $payroll) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <select name="method" class="text-xs border-gray-300 rounded-md">
                                                <option value="cash">Cash</option>
                                                <option value="bank">Bank</option>
                                            </select>
                                            <button type="submit" class="text-indigo-600 hover:underline">Mark Paid</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-500">Paid {{ $payroll->paid_on?->format('Y-m-d') }} via {{ $payroll->method }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-400">No payroll generated for this month yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <a href="{{ route('school.salary-structures.index') }}" class="text-indigo-600 hover:underline">&larr; Manage salary structures</a>
        </div>
    </div>
</x-app-layout>
