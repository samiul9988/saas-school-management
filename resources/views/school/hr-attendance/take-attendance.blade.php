<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Attendance Input') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('school.hr-attendance.take-attendance') }}" class="flex flex-wrap items-end gap-4 mb-6">
                <div>
                    <x-input-label value="Date" />
                    <x-text-input type="date" name="date" value="{{ $date }}" class="mt-1 block" onchange="this.form.submit()" />
                </div>
            </form>

            <form method="POST" action="{{ route('school.hr-attendance.take-attendance.store') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <div class="overflow-x-auto border rounded-md">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Designation</th>
                                <th class="py-3 px-4">In Time</th>
                                <th class="py-3 px-4">Out Time</th>
                                <th class="py-3 px-4">Select For Absence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($teachers as $teacher)
                                <tr class="border-t">
                                    <td class="py-2 px-4">{{ $teacher->staff_serial ?? $teacher->id }}</td>
                                    <td class="py-2 px-4">{{ $teacher->name }}</td>
                                    <td class="py-2 px-4">{{ $teacher->designation }}</td>
                                    <td class="py-2 px-4">
                                        <input type="time" name="in_time[{{ $teacher->id }}]" value="{{ $teacher->attendance?->in_time }}" class="border-gray-300 rounded-md shadow-sm">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="time" name="out_time[{{ $teacher->id }}]" value="{{ $teacher->attendance?->out_time }}" class="border-gray-300 rounded-md shadow-sm">
                                    </td>
                                    <td class="py-2 px-4">
                                        <input type="checkbox" name="absent_ids[]" value="{{ $teacher->id }}" class="h-4 w-4"
                                               {{ $teacher->attendance?->status === 'absent' ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-400">No active staff found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="px-6 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
