<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Device Data') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label value="Date" />
                    <x-text-input type="date" name="date" class="mt-1 block w-full" value="{{ $date }}" required />
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Enroll ID</th>
                        <th class="py-3 px-4">Device Number</th>
                        <th class="py-3 px-4">Records Times</th>
                        <th class="py-3 px-4">Mode</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $row['enroll_id'] }}</td>
                            <td class="py-2 px-4">{{ $row['device_number'] }}</td>
                            <td class="py-2 px-4">{{ $row['recorded_at'] }}</td>
                            <td class="py-2 px-4">{{ $row['mode'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">No Data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
