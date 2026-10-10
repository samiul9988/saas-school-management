<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Mark Input Here') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif

        @if ($error)
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $error }}</div>
        @endif

        @if (session('error') && session('error') !== $error)
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
                <div class="lg:col-span-2">
                <form method="GET" class="p-6 space-y-4">
                    <div>
                        <x-input-label for="section_id" value="Section" />
                        <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Select</option>
                            @foreach ($sections as $sectionOption)
                                <option value="{{ $sectionOption->id }}" @selected(request('section_id') == $sectionOption->id)>
                                    {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="group_name" value="Group" />
                        <select id="group_name" name="group_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">All</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group }}" @selected(request('group_name') === $group)>{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="exam_id" value="Exam" />
                        <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Select</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="subject_id" value="Subject" />
                        <select id="subject_id" name="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Select</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>
                        <i class="bi bi-search mr-1"></i> Search
                    </x-primary-button>
                </form>

                <div class="border-t px-6 pb-6">
                    <form id="mark-input-upload-form" action="{{ route('school.mark-input.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3 pt-4"
                          onsubmit="return window.markInputPrepareUpload(event)">
                        @csrf
                        <input type="hidden" name="section_id" id="upload_section_id">
                        <input type="hidden" name="exam_id" id="upload_exam_id">
                        <input type="hidden" name="subject_id" id="upload_subject_id">
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" class="text-sm" required>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Upload
                        </button>
                    </form>
                    <a href="#" id="mark-input-download-link" onclick="return window.markInputDownload(event)" class="text-indigo-600 hover:underline text-sm inline-block mt-2">
                        Download Excel Format
                    </a>
                </div>
                </div>

                <script>
                    function markInputReadSelection() {
                        var sectionId = document.getElementById('section_id').value;
                        var groupName = document.getElementById('group_name').value;
                        var examId = document.getElementById('exam_id').value;
                        var subjectId = document.getElementById('subject_id').value;

                        if (! sectionId || ! examId || ! subjectId) {
                            alert('Please select Section, Exam and Subject first.');
                            return null;
                        }

                        return { sectionId: sectionId, groupName: groupName, examId: examId, subjectId: subjectId };
                    }

                    window.markInputPrepareUpload = function (event) {
                        var selection = markInputReadSelection();
                        if (! selection) {
                            event.preventDefault();
                            return false;
                        }

                        document.getElementById('upload_section_id').value = selection.sectionId;
                        document.getElementById('upload_exam_id').value = selection.examId;
                        document.getElementById('upload_subject_id').value = selection.subjectId;
                        return true;
                    };

                    window.markInputDownload = function (event) {
                        event.preventDefault();
                        var selection = markInputReadSelection();
                        if (! selection) {
                            return false;
                        }

                        var params = new URLSearchParams({
                            section_id: selection.sectionId,
                            exam_id: selection.examId,
                            subject_id: selection.subjectId,
                            group_name: selection.groupName,
                        });

                        window.location.href = '{{ route('school.mark-input.template') }}?' + params.toString();
                        return false;
                    };
                </script>

                <div class="border-t lg:border-t-0 lg:border-l bg-gray-50 p-6">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-2 px-3">Short Code Title</th>
                                <th class="py-2 px-3">Total</th>
                                <th class="py-2 px-3">Pass Mark</th>
                                <th class="py-2 px-3">Acceptance</th>
                                @if ($examSubject)
                                    <th class="py-2 px-3"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($components as $component)
                                <tr class="border-t bg-white">
                                    <td class="py-2 px-3">{{ $component->shortCode?->name }}</td>
                                    <td class="py-2 px-3">{{ rtrim(rtrim(number_format($component->total, 2), '0'), '.') }}</td>
                                    <td class="py-2 px-3">{{ rtrim(rtrim(number_format($component->pass_mark, 2), '0'), '.') }}</td>
                                    <td class="py-2 px-3">{{ rtrim(rtrim(number_format($component->acceptance, 2), '0'), '.') }}</td>
                                    @if ($examSubject)
                                        <td class="py-2 px-3">
                                            <form action="{{ route('school.subject-components.destroy', $component) }}" method="POST" onsubmit="return confirm('Remove this short code from the subject?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-xs">Remove</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr class="bg-white"><td colspan="5" class="py-4 text-center text-gray-400">{{ $examSubject ? 'No short codes assigned to this subject yet.' : 'Search a subject to see its short codes.' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($examSubject)
                        <form action="{{ route('school.subject-components.store') }}" method="POST" class="mt-4 grid grid-cols-2 gap-2 text-sm">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $examSubject->subject_id }}">
                            <select name="exam_short_code_id" class="col-span-2 border-gray-300 rounded-md shadow-sm text-sm" required>
                                <option value="">Add short code…</option>
                                @foreach ($shortCodes as $shortCode)
                                    <option value="{{ $shortCode->id }}">{{ $shortCode->name }}</option>
                                @endforeach
                            </select>
                            <input type="number" step="0.01" name="total" placeholder="Total" class="border-gray-300 rounded-md shadow-sm text-sm" required>
                            <input type="number" step="0.01" name="pass_mark" placeholder="Pass Mark" class="border-gray-300 rounded-md shadow-sm text-sm" required>
                            <input type="number" step="0.01" name="acceptance" placeholder="Acceptance" value="1" class="border-gray-300 rounded-md shadow-sm text-sm col-span-2" required>
                            <button type="submit" class="col-span-2 bg-gray-800 text-white rounded-md py-1.5 text-sm hover:bg-gray-700">Add Short Code</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if ($examSubject && $components->isNotEmpty())
            <form action="{{ route('school.mark-input.update', $examSubject) }}" method="POST" class="bg-white shadow-sm rounded-lg overflow-hidden">
                @csrf
                @method('PUT')
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Roll</th>
                            @foreach ($components as $component)
                                <th class="py-3 px-4">{{ $component->shortCode?->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $student->id }}</td>
                                <td class="py-2 px-4">{{ $student->name }}</td>
                                <td class="py-2 px-4">{{ $student->roll }}</td>
                                @foreach ($components as $component)
                                    @php
                                        $existing = optional($marks->get($student->id))->components?->firstWhere('subject_component_id', $component->id);
                                    @endphp
                                    <td class="py-2 px-4">
                                        <input type="number" step="0.01" min="0" max="{{ $component->total }}"
                                               name="marks[{{ $student->id }}][{{ $component->id }}]"
                                               value="{{ old('marks.'.$student->id.'.'.$component->id, $existing?->marks_obtained ?? 0) }}"
                                               class="w-20 border-gray-300 rounded-md shadow-sm text-sm">
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ 3 + $components->count() }}" class="py-6 text-center text-gray-400">No students found for this selection.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($students->isNotEmpty())
                    <div class="p-4 flex justify-end">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                @endif
            </form>
        @endif
    </div>
</x-app-layout>
