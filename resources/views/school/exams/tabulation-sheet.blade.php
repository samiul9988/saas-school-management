<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tabulation Sheet') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6 print:hidden">
            <form method="GET" class="grid grid-cols-2 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <x-input-label for="exam_id" value="Exam" />
                    <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($exams as $e)
                            <option value="{{ $e->id }}" @selected(request('exam_id') == $e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="school_class_id" value="Class" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" @selected(request('school_class_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-primary-button>Generate</x-primary-button>
                </div>
            </form>
        </div>

        @if ($rows->isNotEmpty())
            <div class="bg-white shadow-sm rounded-lg p-6 overflow-x-auto">
                <div class="flex justify-between items-center mb-4 print:hidden">
                    <h3 class="font-semibold">Tabulation Sheet</h3>
                    <button onclick="window.print()" class="text-indigo-600 hover:underline text-sm">Print</button>
                </div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-3">Roll</th>
                            <th class="py-2 px-3">Name</th>
                            @foreach ($examSubjects as $examSubject)
                                <th class="py-2 px-3">{{ $examSubject->subject?->name }}<br><span class="text-xs">({{ $examSubject->full_marks }})</span></th>
                            @endforeach
                            <th class="py-2 px-3">Total</th>
                            <th class="py-2 px-3">%</th>
                            <th class="py-2 px-3">Grade</th>
                            <th class="py-2 px-3">Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr class="border-t">
                                <td class="py-2 px-3">{{ $row['student']->roll }}</td>
                                <td class="py-2 px-3">{{ $row['student']->name }}</td>
                                @foreach ($examSubjects as $examSubject)
                                    <td class="py-2 px-3">{{ $row['marks'][$examSubject->id] ?? '—' }}</td>
                                @endforeach
                                <td class="py-2 px-3 font-semibold">{{ $row['total_obtained'] }} / {{ $row['total_full'] }}</td>
                                <td class="py-2 px-3">{{ $row['percentage'] }}%</td>
                                <td class="py-2 px-3">{{ $row['grade'] ?? '—' }}</td>
                                <td class="py-2 px-3">{{ $index + 1 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
