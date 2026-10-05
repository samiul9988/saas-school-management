<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $teacher->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 grid grid-cols-2 gap-4">
                <div><span class="text-gray-500 text-sm">Email</span><div>{{ $teacher->email ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Phone</span><div>{{ $teacher->phone ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Designation</span><div>{{ $teacher->designation ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Status</span><div class="capitalize">{{ $teacher->status }}</div></div>
                <div><span class="text-gray-500 text-sm">Joining Date</span><div>{{ $teacher->joining_date?->format('Y-m-d') ?? '—' }}</div></div>
            </div>

            <a href="{{ route('school.teachers.index') }}" class="text-indigo-600 hover:underline">&larr; Back to teachers</a>
        </div>
    </div>
</x-app-layout>
