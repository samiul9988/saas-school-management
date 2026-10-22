<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Income & Expense Report') }}</h2>
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

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Total Income</p><p class="text-2xl font-bold text-green-600">{{ number_format($totalIncome, 2) }}</p></div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Total Expense</p><p class="text-2xl font-bold text-red-600">{{ number_format($totalExpense, 2) }}</p></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-semibold">Income by Account</div>
                    <table class="w-full text-sm text-left">
                        @forelse ($income as $name => $amount)
                            <tr class="border-t"><td class="py-2 px-4">{{ $name }}</td><td class="py-2 px-4 text-right">{{ number_format($amount, 2) }}</td></tr>
                        @empty
                            <tr><td class="py-4 px-4 text-center text-gray-400">No income.</td></tr>
                        @endforelse
                    </table>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-semibold">Expense by Account</div>
                    <table class="w-full text-sm text-left">
                        @forelse ($expense as $name => $amount)
                            <tr class="border-t"><td class="py-2 px-4">{{ $name }}</td><td class="py-2 px-4 text-right">{{ number_format($amount, 2) }}</td></tr>
                        @empty
                            <tr><td class="py-4 px-4 text-center text-gray-400">No expense.</td></tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
