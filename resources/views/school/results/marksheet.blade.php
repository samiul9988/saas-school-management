<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Marksheet') }} &mdash; {{ $exam->name }}</h2>
    </x-slot>

    <div class="py-12 print:py-0">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 print:shadow-none">
                <div class="flex justify-between mb-6">
                    <div>
                        <div class="font-semibold text-lg">{{ $student->name }}</div>
                        <div class="text-sm text-gray-500">Roll: {{ $student->roll ?? '—' }} &middot; Class: {{ $student->schoolClass?->name }}</div>
                    </div>
                    <button onclick="window.print()" class="print:hidden text-indigo-600 hover:underline text-sm">Print</button>
                </div>

                <table class="w-full text-sm text-left mb-6">
                    <thead>
                        <tr class="text-gray-500 border-b">
                            <th class="py-2">Subject</th>
                            <th class="py-2">Full Marks</th>
                            <th class="py-2">Pass Marks</th>
                            <th class="py-2">Obtained</th>
                            <th class="py-2">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjectRows as $row)
                            <tr class="border-b">
                                <td class="py-2">{{ $row['subject'] }}</td>
                                <td class="py-2">{{ $row['full_marks'] }}</td>
                                <td class="py-2">{{ $row['pass_marks'] }}</td>
                                <td class="py-2">{{ $row['is_absent'] ? 'Absent' : ($row['obtained'] ?? '—') }}</td>
                                <td class="py-2">
                                    <span class="{{ $row['passed'] ? 'text-green-600' : 'text-red-600' }}">{{ $row['passed'] ? 'Pass' : 'Fail' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="grid grid-cols-3 gap-4 text-center border-t pt-4">
                    <div>
                        <div class="text-sm text-gray-500">Total</div>
                        <div class="font-semibold text-lg">{{ $totalObtained }} / {{ $totalFull }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Percentage</div>
                        <div class="font-semibold text-lg">{{ $percentage }}%</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Grade</div>
                        <div class="font-semibold text-lg">{{ $grade?->name ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <a href="{{ route('school.results.index') }}" class="text-indigo-600 hover:underline print:hidden">&larr; Back to results</a>
        </div>
    </div>
</x-app-layout>
