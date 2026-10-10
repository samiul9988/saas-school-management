<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('General Mark Sheet Download') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('school.mark-sheets.general-exam.download') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="section_id" value="Section" />
                        <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($sections as $sectionOption)
                                <option value="{{ $sectionOption->id }}">
                                    {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="exam_id" value="Exam" />
                        <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($exams as $examOption)
                                <option value="{{ $examOption->id }}">{{ $examOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="from_roll" value="From Roll" />
                        <x-text-input id="from_roll" type="number" name="from_roll" class="mt-1 block w-full" value="0" />
                    </div>
                    <div>
                        <x-input-label for="to_roll" value="To Roll" />
                        <x-text-input id="to_roll" type="number" name="to_roll" class="mt-1 block w-full" value="0" />
                    </div>
                </div>

                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-download mr-2"></i> Download
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
