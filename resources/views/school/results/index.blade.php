<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Results') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <x-input-label for="exam_id" value="Exam" />
                        <select id="exam_id" name="exam_id" class="mt-1 border-gray-300 rounded-md shadow-sm">
                            <option value="">— Select exam —</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 border-gray-300 rounded-md shadow-sm">
                            <option value="">— Select class —</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>View Result</x-primary-button>
                </form>
            </div>

            @if ($rows->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr class="text-gray-500">
                                <th class="py-3 px-4">Position</th>
                                <th class="py-3 px-4">Student</th>
                                <th class="py-3 px-4">Total</th>
                                <th class="py-3 px-4">%</th>
                                <th class="py-3 px-4">Grade</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Marksheet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="border-t">
                                    <td class="py-3 px-4">{{ $row['position'] }}</td>
                                    <td class="py-3 px-4">{{ $row['student']->name }}</td>
                                    <td class="py-3 px-4">{{ $row['total_obtained'] }} / {{ $row['total_full'] }}</td>
                                    <td class="py-3 px-4">{{ $row['percentage'] }}%</td>
                                    <td class="py-3 px-4">{{ $row['grade'] ?? '—' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="{{ $row['status'] === 'Pass' ? 'text-green-600' : 'text-red-600' }}">{{ $row['status'] }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="{{ route('school.results.marksheet', [request('exam_id'), $row['student']]) }}" class="text-indigo-600 hover:underline">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif (request('exam_id') && request('school_class_id'))
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-400">No results found.</div>
            @endif
        </div>
    </div>
</x-app-layout>
