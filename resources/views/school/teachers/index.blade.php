<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Teachers') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.teachers.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Teacher
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Designation</th>
                            <th class="py-3 px-4">Phone</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teachers as $teacher)
                            <tr class="border-t">
                                <td class="py-3 px-4">
                                    <a href="{{ route('school.teachers.show', $teacher) }}" class="text-indigo-600 hover:underline">{{ $teacher->name }}</a>
                                </td>
                                <td class="py-3 px-4">{{ $teacher->designation ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $teacher->phone ?? '—' }}</td>
                                <td class="py-3 px-4 capitalize">{{ $teacher->status }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.teachers.edit', $teacher) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('school.teachers.destroy', $teacher) }}" method="POST" class="inline" onsubmit="return confirm('Remove this teacher?')">
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

            {{ $teachers->links() }}
        </div>
    </div>
</x-app-layout>
