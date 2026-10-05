<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $exam->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    {{ $exam->academicYear?->name ?? 'No academic year' }} &middot;
                    {{ $exam->starts_on?->format('Y-m-d') }} &ndash; {{ $exam->ends_on?->format('Y-m-d') }} &middot;
                    <span class="capitalize">{{ $exam->status }}</span>
                </div>
                <a href="{{ route('school.exams.subjects.create', $exam) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Assign Subject
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Full Marks</th>
                            <th class="py-3 px-4">Pass Marks</th>
                            <th class="py-3 px-4">Exam Date</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($exam->examSubjects as $examSubject)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $examSubject->schoolClass?->name }}</td>
                                <td class="py-3 px-4">{{ $examSubject->subject?->name }}</td>
                                <td class="py-3 px-4">{{ $examSubject->full_marks }}</td>
                                <td class="py-3 px-4">{{ $examSubject->pass_marks }}</td>
                                <td class="py-3 px-4">{{ $examSubject->exam_date?->format('Y-m-d') ?? '—' }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.marks.edit', $examSubject) }}" class="text-indigo-600 hover:underline">Enter Marks</a>
                                    <form action="{{ route('school.exams.subjects.destroy', [$exam, $examSubject]) }}" method="POST" class="inline" onsubmit="return confirm('Remove this subject from the exam?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-gray-400">No subjects assigned yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <a href="{{ route('school.results.index') }}?exam_id={{ $exam->id }}" class="text-indigo-600 hover:underline">View class results for this exam &rarr;</a>
        </div>
    </div>
</x-app-layout>
