<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('General Exam Result Process') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="POST" action="{{ route('school.results-process.general-exam.process') }}" class="max-w-md space-y-4">
                @csrf
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
                <div>
                    <x-input-label for="exam_id" value="Exam" />
                    <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($exams as $exam)
                            <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    Process
                </button>
            </form>
        </div>

        @if ($section)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Position</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Percentage</th>
                            <th class="py-3 px-4">GPA</th>
                            <th class="py-3 px-4">Grade</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $row->position ?? '—' }}</td>
                                <td class="py-2 px-4">{{ $row->student->roll }}</td>
                                <td class="py-2 px-4">{{ $row->student->name }}</td>
                                <td class="py-2 px-4">{{ $row->total_obtained }} / {{ $row->total_full }}</td>
                                <td class="py-2 px-4">{{ $row->percentage }}%</td>
                                <td class="py-2 px-4">{{ $row->gpa }}</td>
                                <td class="py-2 px-4">{{ $row->grade?->name ?? '—' }}</td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $row->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $row->is_passed ? 'Pass' : 'Fail' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-6 text-center text-gray-400">Not processed yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
