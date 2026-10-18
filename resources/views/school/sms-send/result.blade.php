<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Send Result SMS') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <x-input-label for="academic_year_id" value="Academic Year" />
                        <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(request('academic_year_id') == $year->id)>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Select --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="exam_id" value="Exam" />
                        <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Select --</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>Search</x-primary-button>
                </form>
            </div>

            <form method="POST" action="{{ route('school.sms-send.result.store') }}">
                @csrf
                <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">
                <input type="hidden" name="school_class_id" value="{{ request('school_class_id') }}">

                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <x-input-label for="content" value="SMS Content" />
                    <textarea id="content" name="content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('content', $defaultContent) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Available tags: @{{student_name}}, @{{class}}, @{{exam_name}}, @{{total_marks}}, @{{grade}}, @{{gpa}}</p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr class="text-gray-500">
                                <th class="py-2 px-4"><input type="checkbox" onclick="document.querySelectorAll('.result-cb').forEach(c=>c.checked=this.checked)"></th>
                                <th class="py-2 px-4">Student</th>
                                <th class="py-2 px-4">Roll</th>
                                <th class="py-2 px-4">Mobile</th>
                                <th class="py-2 px-4">Total Marks</th>
                                <th class="py-2 px-4">Grade</th>
                                <th class="py-2 px-4">GPA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($results as $result)
                                <tr class="border-t">
                                    <td class="py-2 px-4"><input class="result-cb" type="checkbox" name="student_ids[]" value="{{ $result['student']->id }}"></td>
                                    <td class="py-2 px-4">{{ $result['student']->name }}</td>
                                    <td class="py-2 px-4">{{ $result['student']->roll }}</td>
                                    <td class="py-2 px-4">{{ $result['student']->guardian_phone }}</td>
                                    <td class="py-2 px-4">{{ $result['total_obtained'] }}/{{ $result['total_full'] }}</td>
                                    <td class="py-2 px-4">{{ $result['grade'] }}</td>
                                    <td class="py-2 px-4">{{ $result['gpa'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-4 px-4 text-center text-gray-400">No data. Select a class and exam above.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4 flex justify-end">
                        <x-primary-button>Send SMS</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
