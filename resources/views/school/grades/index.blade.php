<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Grading Scale') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.grades.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Grade
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Grade</th>
                            <th class="py-3 px-4">Min %</th>
                            <th class="py-3 px-4">Max %</th>
                            <th class="py-3 px-4">Grade Point</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grades as $grade)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $grade->name }}</td>
                                <td class="py-3 px-4">{{ $grade->min_percent }}</td>
                                <td class="py-3 px-4">{{ $grade->max_percent }}</td>
                                <td class="py-3 px-4">{{ $grade->grade_point }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.grades.edit', $grade) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('school.grades.destroy', $grade) }}" method="POST" class="inline" onsubmit="return confirm('Delete this grade?')">
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
