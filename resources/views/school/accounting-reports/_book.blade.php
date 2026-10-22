{{-- Shared partial for cash/bank book reports. Expects: $title, $routeName, $from, $to, $account, $openingBalance, $rows, $closingBalance --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            @unless ($account)
                <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded">This account has not been set up yet. Visit Chart of Accounts to seed defaults.</div>
            @endunless

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Opening Balance</p><p class="text-xl font-bold">{{ number_format($openingBalance, 2) }}</p></div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Closing Balance</p><p class="text-xl font-bold">{{ number_format($closingBalance, 2) }}</p></div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-4">Date</th>
                            <th class="py-2 px-4">Description</th>
                            <th class="py-2 px-4">Debit (In)</th>
                            <th class="py-2 px-4">Credit (Out)</th>
                            <th class="py-2 px-4">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                                <td class="py-2 px-4">{{ $row['description'] }}</td>
                                <td class="py-2 px-4">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                                <td class="py-2 px-4">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                                <td class="py-2 px-4">{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 px-4 text-center text-gray-400">No transactions in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
