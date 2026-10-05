<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $student->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 grid grid-cols-2 gap-4">
                <div><span class="text-gray-500 text-sm">Admission No.</span><div>{{ $student->admission_number ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Roll</span><div>{{ $student->roll ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Class</span><div>{{ $student->schoolClass?->name ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Section</span><div>{{ $student->section?->name ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Academic Year</span><div>{{ $student->academicYear?->name ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Status</span><div class="capitalize">{{ $student->status }}</div></div>
                <div><span class="text-gray-500 text-sm">Guardian</span><div>{{ $student->guardian_name ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Guardian Phone</span><div>{{ $student->guardian_phone ?? '—' }}</div></div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Recent Attendance</h3>
                <table class="w-full text-sm text-left">
                    <thead><tr class="text-gray-500 border-b"><th class="py-2">Date</th><th class="py-2">Status</th></tr></thead>
                    <tbody>
                        @forelse ($student->attendances->sortByDesc('date')->take(10) as $attendance)
                            <tr class="border-b"><td class="py-2">{{ $attendance->date->format('Y-m-d') }}</td><td class="py-2 capitalize">{{ $attendance->status }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="py-4 text-center text-gray-400">No attendance recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <a href="{{ route('school.students.index') }}" class="text-indigo-600 hover:underline">&larr; Back to students</a>
        </div>
    </div>
</x-app-layout>
