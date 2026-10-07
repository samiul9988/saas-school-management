<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Mark Input / Update') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="font-semibold mb-4">Search</h3>
            <form method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="school_class_id" value="Class" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="exam_id" value="Exam" />
                    <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select</option>
                        @foreach ($exams as $exam)
                            <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="subject_id" value="Subject" />
                    <select id="subject_id" name="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-primary-button>Search</x-primary-button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50">
                    <tr class="text-gray-500">
                        <th class="py-3 px-4">Class</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Exam</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Pass Mark</th>
                        <th class="py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($results as $examSubject)
                        <tr class="border-t">
                            <td class="py-3 px-4">{{ $examSubject->schoolClass?->name }}</td>
                            <td class="py-3 px-4">{{ $examSubject->subject?->name }}</td>
                            <td class="py-3 px-4">{{ $examSubject->exam?->name }}</td>
                            <td class="py-3 px-4">{{ $examSubject->full_marks }}</td>
                            <td class="py-3 px-4">{{ $examSubject->pass_marks }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('school.marks.edit', $examSubject) }}" class="text-indigo-600 hover:underline">Enter / Update Marks</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-400">No Data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
