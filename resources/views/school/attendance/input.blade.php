<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Attendance Input') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
            <form method="GET" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                <div>
                    <x-text-input type="date" name="date" class="block w-full" value="{{ $date }}" required />
                </div>
                <div>
                    <select name="section_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                {{ $sectionOption->schoolClass?->name }} -{{ $sectionOption->shift?->name ?? 'N/A' }}-{{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="period_id" class="block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Period</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected((string) request('period_id') === (string) $period->id)>{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>

            @if ($section)
                <form method="POST" action="{{ route('school.student-attendance.input.store') }}" class="bg-white shadow-sm rounded-lg overflow-hidden">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <input type="hidden" name="section_id" value="{{ $section->id }}">
                    <input type="hidden" name="period_id" value="{{ request('period_id') }}">

                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-3 px-4">Student ID</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Roll</th>
                                <th class="py-3 px-4">Mark Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr class="border-t">
                                    <td class="py-2 px-4">{{ $student->admission_number }}</td>
                                    <td class="py-2 px-4">{{ $student->name }}</td>
                                    <td class="py-2 px-4">{{ $student->roll }}</td>
                                    <td class="py-2 px-4"><input type="checkbox" name="absent_ids[]" value="{{ $student->id }}"></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-6 text-center text-gray-400">No students found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($students->isNotEmpty())
                        <div class="p-4">
                            <button type="submit" class="px-8 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700">
                                Submit
                            </button>
                        </div>
                    @endif
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
