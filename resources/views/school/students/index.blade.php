<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Students') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-between items-center">
                <form method="GET" class="flex gap-2">
                    <select name="school_class_id" class="border-gray-300 rounded-md shadow-sm text-sm" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('school.students.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Admit Student
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Admission No.</th>
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Section</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr class="border-t">
                                <td class="py-3 px-4">
                                    <a href="{{ route('school.students.show', $student) }}" class="text-indigo-600 hover:underline">{{ $student->name }}</a>
                                </td>
                                <td class="py-3 px-4">{{ $student->admission_number ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $student->schoolClass?->name ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $student->roll ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $student->section?->name ?? '—' }}</td>
                                <td class="py-3 px-4 capitalize">{{ $student->status }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.students.edit', $student) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('school.students.destroy', $student) }}" method="POST" class="inline" onsubmit="return confirm('Remove this student?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $students->links() }}
        </div>
    </div>
</x-app-layout>
