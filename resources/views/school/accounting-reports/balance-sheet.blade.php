<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Balance Sheet') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-6 flex items-end gap-4">
                <div>
                    <x-input-label for="as_of" value="As Of" />
                    <x-text-input id="as_of" name="as_of" type="date" class="mt-1 block w-full" value="{{ $asOf->format('Y-m-d') }}" />
                </div>
                <x-primary-button>Filter</x-primary-button>
            </form>

            @if (round($totalAssets, 2) != round($totalLiabilitiesAndEquity, 2))
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">Warning: assets do not equal liabilities + equity. Review journal entries.</div>
            @endif

            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-3">Assets</h3>
                    @foreach ($assets as $row)
                        <div class="flex justify-between text-sm py-1"><span>{{ $row['account']->name }}</span><span>{{ number_format($row['balance'], 2) }}</span></div>
                    @endforeach
                    <hr class="my-2">
                    <div class="flex justify-between font-semibold"><span>Total Assets</span><span>{{ number_format($totalAssets, 2) }}</span></div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-3">Liabilities</h3>
                    @foreach ($liabilities as $row)
                        <div class="flex justify-between text-sm py-1"><span>{{ $row['account']->name }}</span><span>{{ number_format($row['balance'], 2) }}</span></div>
                    @endforeach
                    <h3 class="font-semibold mb-3 mt-4">Equity</h3>
                    @foreach ($equity as $row)
                        <div class="flex justify-between text-sm py-1"><span>{{ $row['account']->name }}</span><span>{{ number_format($row['balance'], 2) }}</span></div>
                    @endforeach
                    <div class="flex justify-between text-sm py-1"><span>Retained Earnings (Income - Expense)</span><span>{{ number_format($retainedEarnings, 2) }}</span></div>
                    <hr class="my-2">
                    <div class="flex justify-between font-semibold"><span>Total Liabilities &amp; Equity</span><span>{{ number_format($totalLiabilitiesAndEquity, 2) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
