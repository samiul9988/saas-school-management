<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Leave Input') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-64">
                    <x-text-input name="student_id" class="block w-full" value="{{ request('student_id') }}" placeholder="Enter Student ID" />
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>

        @if ($student)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="font-semibold mb-4">{{ $student->name }} <span class="text-gray-400 font-normal">({{ $student->admission_number }})</span></div>
                <form method="POST" action="{{ route('school.student-attendance.leave.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <div>
                        <x-input-label value="From Date" />
                        <x-text-input type="date" name="from_date" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label value="To Date" />
                        <x-text-input type="date" name="to_date" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <button type="submit" class="w-full px-6 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                            Save Leave
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $attendance->date->format('d-M-Y') }}</td>
                                <td class="py-2 px-4 text-amber-600 font-semibold">Leave</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-6 text-center text-gray-400">No leave records yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif (request()->filled('student_id'))
            <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-400">No student found with that ID.</div>
        @endif
    </div>
</x-app-layout>
