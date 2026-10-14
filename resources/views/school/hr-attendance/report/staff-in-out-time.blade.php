<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Attendance Time Configuration Report') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('school.hr-attendance.report.staff-in-out-time') }}" class="flex flex-wrap items-end gap-4 mb-6">
                <div>
                    <x-input-label value="Week Of" />
                    <x-text-input type="date" name="date" value="{{ $weekStart->toDateString() }}" class="mt-1 block" onchange="this.form.submit()" />
                </div>
            </form>

            <div class="overflow-x-auto border rounded-md">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Designation</th>
                            @foreach (\App\Http\Controllers\School\StaffAttendanceController::WEEKDAYS as $day)
                                <th class="py-3 px-4">{{ ucfirst($day) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $teacher)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $teacher->staff_serial ?? $teacher->id }}</td>
                                <td class="py-2 px-4">{{ $teacher->name }}</td>
                                <td class="py-2 px-4">{{ $teacher->designation }}</td>
                                @foreach (\App\Http\Controllers\School\StaffAttendanceController::WEEKDAYS as $day)
                                    <td class="py-2 px-4 whitespace-nowrap">
                                        @php($entry = $teacher->week->get($day))
                                        @if ($entry)
                                            {{ $entry->in_time }} - {{ $entry->out_time }}
                                        @else
                                            <span class="text-gray-400">--</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="10" class="py-6 text-center text-gray-400">No Data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
