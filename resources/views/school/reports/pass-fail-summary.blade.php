<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Section Wise Pass/Fail Summary') }}</h2>
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

        @if ($exam && ! $resultBatch)
            <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded">
                This exam hasn't been processed yet. Go to Result Process &rarr; General Exam and click "Process Result" first.
            </div>
        @endif

        @if ($resultBatch)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Class - Section</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Passed</th>
                            <th class="py-3 px-4">Failed</th>
                            <th class="py-3 px-4">Pass %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary as $row)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $row['section']?->schoolClass?->name }} - {{ $row['section']?->name }}</td>
                                <td class="py-2 px-4">{{ $row['total'] }}</td>
                                <td class="py-2 px-4 text-green-700">{{ $row['passed'] }}</td>
                                <td class="py-2 px-4 text-red-700">{{ $row['failed'] }}</td>
                                <td class="py-2 px-4 font-semibold">{{ $row['pass_percent'] }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-gray-400">No processed results found for this exam.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
