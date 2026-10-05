<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Sections') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.sections.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Section
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Section</th>
                            <th class="py-3 px-4">Capacity</th>
                            <th class="py-3 px-4">Students</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sections as $section)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $section->schoolClass?->name }}</td>
                                <td class="py-3 px-4">{{ $section->name }}</td>
                                <td class="py-3 px-4">{{ $section->capacity ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $section->students_count }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.sections.edit', $section) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('school.sections.destroy', $section) }}" method="POST" class="inline" onsubmit="return confirm('Delete this section?')">
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
        </div>
    </div>
</x-app-layout>
