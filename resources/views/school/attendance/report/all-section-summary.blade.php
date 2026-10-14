<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Section Wise Daily Summary') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <x-text-input type="date" name="date" class="block w-full" value="{{ $date }}" required />
                </div>
                <div>
                    <select name="period_id" class="block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Period</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected((string) $periodId === (string) $period->id)>{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Class</th>
                        <th class="py-3 px-4">Shift</th>
                        <th class="py-3 px-4">Section</th>
                        <th class="py-3 px-4">Total Attendance</th>
                        <th class="py-3 px-4">Present</th>
                        <th class="py-3 px-4">Absent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $row['class'] }}</td>
                            <td class="py-2 px-4">{{ $row['shift'] }}</td>
                            <td class="py-2 px-4">{{ $row['section'] }}</td>
                            <td class="py-2 px-4">{{ $row['total'] }}</td>
                            <td class="py-2 px-4">{{ $row['present'] }}</td>
                            <td class="py-2 px-4">{{ $row['absent'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-400">No Data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
