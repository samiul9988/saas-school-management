<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('SMS Usage Report') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Total</p><p class="text-xl font-bold">{{ number_format($summary['total']) }}</p></div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Sent</p><p class="text-xl font-bold text-green-600">{{ number_format($summary['sent']) }}</p></div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Failed</p><p class="text-xl font-bold text-red-600">{{ number_format($summary['failed']) }}</p></div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Unknown</p><p class="text-xl font-bold text-yellow-600">{{ number_format($summary['unknown']) }}</p></div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Credits Consumed</p><p class="text-xl font-bold">{{ number_format($summary['credits_consumed']) }}</p></div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">By School</h3>
                <table class="w-full text-sm text-left">
                    <thead><tr class="text-gray-500 border-b"><th class="py-2">School</th><th class="py-2">Status</th><th class="py-2">Count</th><th class="py-2">Credits</th></tr></thead>
                    <tbody>
                        @forelse ($bySchool as $schoolId => $rows)
                            @foreach ($rows as $row)
                                <tr class="border-b">
                                    <td class="py-2">{{ $row->school->name ?? 'Unknown' }}</td>
                                    <td class="py-2 capitalize">{{ $row->status }}</td>
                                    <td class="py-2">{{ $row->total }}</td>
                                    <td class="py-2">{{ $row->credits }}</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-400">No activity in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
