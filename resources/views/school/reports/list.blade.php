<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __($title) }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <x-input-label for="academic_year_id" value="Academic Year" />
                    <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">All</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}" @selected(request('academic_year_id') == $year->id)>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($scopeType === 'class')
                    <div>
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <x-input-label for="section_id" value="Section" />
                        <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($sections as $sectionOption)
                                <option value="{{ $sectionOption->id }}" @selected(request('section_id') == $sectionOption->id)>
                                    {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

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

        @if ($resultBatch === null && request()->filled('exam_id'))
            <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded">
                This exam hasn't been processed yet. Go to Result Process &rarr; General Exam and click "Process Result" first.
            </div>
        @endif

        @if ($resultBatch)
            @if ($scopeType === 'section' && ! $failedOnly && $sort === 'roll')
                <div class="flex justify-end gap-2">
                    <a href="{{ route('school.reports.section-result.pdf', request()->only('exam_id', 'section_id')) }}" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                        <i class="bi bi-file-earmark-pdf mr-1"></i> Download PDF
                    </a>
                    <a href="{{ route('school.reports.section-result.excel', request()->only('exam_id', 'section_id')) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                        <i class="bi bi-file-earmark-excel mr-1"></i> Download Excel
                    </a>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            @if ($sort === 'merit')
                                <th class="py-3 px-4">Merit</th>
                            @endif
                            <th class="py-3 px-4">ID No</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Percentage</th>
                            <th class="py-3 px-4">GPA</th>
                            <th class="py-3 px-4">Grade</th>
                            @if ($sort === 'merit')
                                <th class="py-3 px-4">Position</th>
                            @endif
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Total Failed Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $index => $row)
                            <tr class="border-t">
                                @if ($sort === 'merit')
                                    <td class="py-2 px-4 font-semibold">{{ $row->is_passed ? $index + 1 : '—' }}</td>
                                @endif
                                <td class="py-2 px-4">{{ $row->student->admission_number }}</td>
                                <td class="py-2 px-4">{{ $row->student->roll }}</td>
                                <td class="py-2 px-4">{{ $row->student->name }}</td>
                                <td class="py-2 px-4">{{ $row->total_obtained }} / {{ $row->total_full }}</td>
                                <td class="py-2 px-4">{{ $row->percentage }}%</td>
                                <td class="py-2 px-4">{{ $row->gpa }}</td>
                                <td class="py-2 px-4">{{ $row->grade?->name ?? '—' }}</td>
                                @if ($sort === 'merit')
                                    <td class="py-2 px-4">{{ $row->is_passed ? $row->position : '—' }}</td>
                                @endif
                                <td class="py-2 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $row->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $row->is_passed ? 'Pass' : 'Fail' }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">{{ $row->failed_subjects_count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $sort === 'merit' ? 10 : 9 }}" class="py-6 text-center text-gray-400">
                                {{ $failedOnly ? 'No failed students found for this selection.' : 'No processed results found for this selection.' }}
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
