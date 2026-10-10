<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Un Assign Mark') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="exam_id" value="Exam" />
                    <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($exams as $examOption)
                            <option value="{{ $examOption->id }}" @selected(request('exam_id') == $examOption->id)>{{ $examOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button>
                    <i class="bi bi-search mr-1"></i> Search
                </x-primary-button>
            </form>
        </div>

        @if ($exam)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Total Students</th>
                            <th class="py-3 px-4">Marked</th>
                            <th class="py-3 px-4">Missing</th>
                            <th class="py-3 px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $row['class'] }}</td>
                                <td class="py-2 px-4">{{ $row['subject'] }}</td>
                                <td class="py-2 px-4">{{ $row['total_students'] }}</td>
                                <td class="py-2 px-4">{{ $row['marked'] }}</td>
                                <td class="py-2 px-4"><span class="text-red-600 font-semibold">{{ $row['missing'] }}</span></td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('school.marks.edit', $row['exam_subject_id']) }}" class="text-indigo-600 hover:underline">Enter Marks</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-400">All subjects for this exam are fully marked.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
