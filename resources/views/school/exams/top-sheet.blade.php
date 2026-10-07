<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Top Sheet') }}</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b font-semibold">Exam Mark Input Blank Sheet Download</div>
        <div class="p-6">
            <form method="GET" action="{{ route('school.exams.top-sheet.download') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="section_id" value="Section List" />
                    <select id="section_id" name="section_id" class="mt-1 border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->schoolClass?->name }} - {{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="exam_id" value="Exam" />
                    <select id="exam_id" name="exam_id" class="mt-1 border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                    <i class="bi bi-download"></i> Download
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
