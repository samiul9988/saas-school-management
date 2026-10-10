<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Grand Final Process') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="POST" action="{{ route('school.results-process.grand-final.process') }}" class="flex flex-wrap gap-4 items-end">
                @csrf
                <div>
                    <x-input-label for="school_class_id" value="Class *" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($classes as $classOption)
                            <option value="{{ $classOption->id }}" @selected(request('school_class_id') == $classOption->id)>{{ $classOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="result_batch_id" value="Exam *" />
                    <select id="result_batch_id" name="result_batch_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($grandFinals as $grandFinalOption)
                            <option value="{{ $grandFinalOption->id }}" @selected(request('result_batch_id') == $grandFinalOption->id)>{{ $grandFinalOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600">
                    <i class="bi bi-gear mr-1"></i> Process
                </button>
            </form>

            <a href="{{ route('school.exam-configuration.index') }}" class="text-indigo-600 hover:underline text-xs inline-block mt-3">+ Exam Configuration (create new / edit component exams)</a>
        </div>

        @if ($class)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Position</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Weighted %</th>
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
                            <tr><td colspan="7" class="py-6 text-center text-gray-400">Not processed yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
