<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Fee Collection & Outstanding Dues') }}</h2>
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

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Collected (this range)</p><p class="text-2xl font-bold text-green-600">{{ number_format($collected, 2) }}</p></div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Outstanding Dues (overall)</p><p class="text-2xl font-bold text-red-600">{{ number_format($outstanding, 2) }}</p></div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 font-semibold">By Payment Method</div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-4">Method</th>
                            <th class="py-2 px-4">Count</th>
                            <th class="py-2 px-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byMethod as $row)
                            <tr class="border-t">
                                <td class="py-2 px-4 capitalize">{{ $row->method }}</td>
                                <td class="py-2 px-4">{{ $row->count }}</td>
                                <td class="py-2 px-4">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 px-4 text-center text-gray-400">No payments in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
