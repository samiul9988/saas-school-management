<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Profit & Loss Statement') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <div class="flex justify-between"><span>Total Income</span><span class="font-semibold">{{ number_format($totalIncome, 2) }}</span></div>
                <div class="flex justify-between"><span>Total Expense</span><span class="font-semibold">{{ number_format($totalExpense, 2) }}</span></div>
                <hr>
                <div class="flex justify-between text-lg font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <span>{{ $netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</span>
                    <span>{{ number_format(abs($netProfit), 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
