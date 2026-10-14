<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Date Wise Staff Attendance Report') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('school.hr-attendance.report.date-wise') }}" class="flex flex-wrap items-end gap-4 mb-6">
                <div>
                    <x-input-label value="Select Date" />
                    <x-text-input type="date" name="date" value="{{ $date }}" class="mt-1 block" />
                </div>
                <button type="submit" class="px-6 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    Search
                </button>
            </form>

            <div class="overflow-x-auto border rounded-md">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">In Time</th>
                            <th class="py-3 px-4">In Status</th>
                            <th class="py-3 px-4">Out Time</th>
                            <th class="py-3 px-4">Out Status</th>
                            <th class="py-3 px-4">Mobile No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $record->teacher?->staff_serial ?? $record->teacher_id }}</td>
                                <td class="py-2 px-4">{{ $record->teacher?->name }}</td>
                                <td class="py-2 px-4">{{ ucfirst($record->status) }}</td>
                                <td class="py-2 px-4">{{ $record->in_time }}</td>
                                <td class="py-2 px-4">{{ $record->in_status ? ucfirst(str_replace('_', ' ', $record->in_status)) : '--' }}</td>
                                <td class="py-2 px-4">{{ $record->out_time }}</td>
                                <td class="py-2 px-4">{{ $record->out_status ? ucfirst(str_replace('_', ' ', $record->out_status)) : '--' }}</td>
                                <td class="py-2 px-4">{{ $record->teacher?->phone }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-6 text-center text-gray-400">No Data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
