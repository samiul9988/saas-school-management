<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Basic Update') }}</h2>
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
                    <select name="section_id" class="block w-full border-gray-300 rounded-md shadow-sm" required onchange="this.form.submit()">
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>

        @if ($section)
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
                        @forelse ($students as $student)
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
                        @empty
                            <tr><td colspan="7" class="py-6 text-center text-gray-400">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
