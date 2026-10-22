<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Trial Balance') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-6 flex items-end gap-4">
                <div>
                    <x-input-label for="from" value="From" />
                    <x-text-input id="from" name="from" type="date" class="mt-1 block w-full" value="{{ $from->format('Y-m-d') }}" />
                </div>
                <div>
                    <x-input-label for="to" value="To" />
                    <x-text-input id="to" name="to" type="date" class="mt-1 block w-full" value="{{ $to->format('Y-m-d') }}" />
                </div>
                <x-primary-button>Filter</x-primary-button>
            </form>

            @if ($totalDebit != $totalCredit)
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">Warning: debits and credits do not match. This should never happen under normal operation — please review recent journal entries.</div>
            @else
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">Balanced: total debit equals total credit.</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-4">Code</th>
                            <th class="py-2 px-4">Account</th>
                            <th class="py-2 px-4">Debit</th>
                            <th class="py-2 px-4">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $row['account']->code }}</td>
                                <td class="py-2 px-4">{{ $row['account']->name }}</td>
                                <td class="py-2 px-4">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                                <td class="py-2 px-4">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 px-4 text-center text-gray-400">No posted entries in this range.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t font-semibold">
                            <td class="py-2 px-4" colspan="2">Total</td>
                            <td class="py-2 px-4">{{ number_format($totalDebit, 2) }}</td>
                            <td class="py-2 px-4">{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
