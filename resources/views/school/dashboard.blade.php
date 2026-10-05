<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('School Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Students</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_students'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Teachers</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_teachers'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Present Today</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['present_today'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Absent Today</div>
                    <div class="text-3xl font-bold text-red-600">{{ $stats['absent_today'] }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">Recently Admitted Students</h3>
                    <a href="{{ route('school.students.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                </div>
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b text-gray-500">
                            <th class="py-2">Name</th>
                            <th class="py-2">Class</th>
                            <th class="py-2">Section</th>
                            <th class="py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentStudents as $student)
                            <tr class="border-b">
                                <td class="py-2">
                                    <a href="{{ route('school.students.show', $student) }}" class="text-indigo-600 hover:underline">{{ $student->name }}</a>
                                </td>
                                <td class="py-2">{{ $student->schoolClass?->name ?? '—' }}</td>
                                <td class="py-2">{{ $student->section?->name ?? '—' }}</td>
                                <td class="py-2 capitalize">{{ $student->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-400">No students yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
