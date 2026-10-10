<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Exam Fourth Subject Assign') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex gap-6 border-b mb-6">
                <a href="{{ route('school.exam-configuration.fourth-subject', ['tab' => 'assign']) }}"
                   class="pb-2 text-sm font-semibold {{ $tab === 'assign' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500' }}">
                    Assign
                </a>
                <a href="{{ route('school.exam-configuration.fourth-subject', ['tab' => 'list']) }}"
                   class="pb-2 text-sm font-semibold {{ $tab === 'list' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500' }}">
                    Assign List
                </a>
            </div>

            @if ($tab === 'assign')
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end mb-6">
                    <input type="hidden" name="tab" value="assign">
                    <div>
                        <x-input-label for="section_id" value="Section" />
                        <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($sections as $sectionOption)
                                <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                    {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="group_name" value="Group" />
                        <select id="group_name" name="group_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group }}" @selected($groupName === $group)>{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>

                @if ($section && $groupName)
                    <form method="POST" action="{{ route('school.exam-configuration.fourth-subject.store') }}">
                        @csrf
                        <input type="hidden" name="section_id" value="{{ $section->id }}">
                        <input type="hidden" name="group_name" value="{{ $groupName }}">

                        <div class="overflow-x-auto border rounded-md">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-blue-100">
                                    <tr class="text-gray-700">
                                        <th class="py-3 px-4"><input type="checkbox" id="select-all-students"></th>
                                        <th class="py-3 px-4">Student ID</th>
                                        <th class="py-3 px-4">Name</th>
                                        <th class="py-3 px-4">Roll</th>
                                        <th class="py-3 px-4">Group</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($students as $student)
                                        <tr class="border-t">
                                            <td class="py-2 px-4"><input type="checkbox" class="student-checkbox" name="student_ids[]" value="{{ $student->id }}"></td>
                                            <td class="py-2 px-4">{{ $student->admission_number }}</td>
                                            <td class="py-2 px-4">{{ $student->name }}</td>
                                            <td class="py-2 px-4">{{ $student->roll }}</td>
                                            <td class="py-2 px-4">{{ $student->group_name }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-6 text-center text-gray-400">No students found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($students->isNotEmpty())
                            <div class="mt-4">
                                <button type="button" id="open-process-modal" class="px-8 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700"
                                        data-bs-toggle="modal" data-bs-target="#process-modal">
                                    Process
                                </button>
                            </div>
                        @endif

                        <div class="modal fade" id="process-modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Assign Fourth Subject</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                            <div>
                                                <div class="font-semibold text-sm mb-2 pb-1 border-b">Check For Compulsory</div>
                                                <div class="space-y-2">
                                                    @foreach ($subjects as $subject)
                                                        <label class="flex items-center gap-2 text-sm">
                                                            <input type="checkbox" name="compulsory_subject_ids[]" value="{{ $subject->id }}"
                                                                   @checked(in_array($subject->id, $existingBySubject['Compulsory'] ?? []))>
                                                            {{ $subject->name }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-sm mb-2 pb-1 border-b">Check For Optional</div>
                                                <div class="space-y-2">
                                                    @foreach ($subjects as $subject)
                                                        <label class="flex items-center gap-2 text-sm">
                                                            <input type="checkbox" name="optional_subject_ids[]" value="{{ $subject->id }}"
                                                                   @checked(in_array($subject->id, $existingBySubject['Optional'] ?? []))>
                                                            {{ $subject->name }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Assign</button>
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <script>
                        (function () {
                            var selectAll = document.getElementById('select-all-students');
                            if (selectAll) {
                                selectAll.addEventListener('change', function () {
                                    document.querySelectorAll('.student-checkbox').forEach(function (cb) {
                                        cb.checked = selectAll.checked;
                                    });
                                });
                            }

                            var openBtn = document.getElementById('open-process-modal');
                            var modalEl = document.getElementById('process-modal');
                            if (openBtn && modalEl) {
                                openBtn.addEventListener('click', function () {
                                    if (window.bootstrap && window.bootstrap.Modal) {
                                        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
                                        return;
                                    }
                                    // Fallback if the Bootstrap JS bundle hasn't loaded/initialized yet.
                                    modalEl.classList.add('show');
                                    modalEl.style.display = 'block';
                                    modalEl.removeAttribute('aria-hidden');
                                    document.body.classList.add('modal-open');

                                    var backdrop = document.createElement('div');
                                    backdrop.className = 'modal-backdrop fade show';
                                    backdrop.id = 'process-modal-backdrop';
                                    document.body.appendChild(backdrop);

                                    var closeModal = function () {
                                        modalEl.classList.remove('show');
                                        modalEl.style.display = 'none';
                                        modalEl.setAttribute('aria-hidden', 'true');
                                        document.body.classList.remove('modal-open');
                                        var bd = document.getElementById('process-modal-backdrop');
                                        if (bd) bd.remove();
                                    };

                                    modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function (btn) {
                                        btn.addEventListener('click', closeModal, { once: true });
                                    });
                                    backdrop.addEventListener('click', closeModal, { once: true });
                                });
                            }
                        })();
                    </script>
                @endif
            @else
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end mb-6">
                    <input type="hidden" name="tab" value="list">
                    <div>
                        <x-input-label for="list_section_id" value="Section" />
                        <select id="list_section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($sections as $sectionOption)
                                <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                    {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>

                <div class="overflow-x-auto border rounded-md">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-3 px-4"><input type="checkbox" id="select-all-list"></th>
                                <th class="py-3 px-4">Student ID</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Roll</th>
                                <th class="py-3 px-4">Group</th>
                                <th class="py-3 px-4">Subject Name</th>
                                <th class="py-3 px-4">Subject Status</th>
                                <th class="py-3 px-4">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assignments as $assignment)
                                <tr class="border-t">
                                    <td class="py-2 px-4"><input type="checkbox" class="list-checkbox"></td>
                                    <td class="py-2 px-4">{{ $assignment->student?->admission_number }}</td>
                                    <td class="py-2 px-4">{{ $assignment->student?->name }}</td>
                                    <td class="py-2 px-4">{{ $assignment->student?->roll }}</td>
                                    <td class="py-2 px-4">{{ $assignment->student?->group_name }}</td>
                                    <td class="py-2 px-4">{{ $assignment->subject?->name }}</td>
                                    <td class="py-2 px-4">{{ $assignment->subject_type }}</td>
                                    <td class="py-2 px-4">
                                        <form action="{{ route('school.exam-configuration.fourth-subject.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Remove this assignment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="py-10 text-center text-gray-400">No Data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <script>
                    (function () {
                        var selectAll = document.getElementById('select-all-list');
                        if (selectAll) {
                            selectAll.addEventListener('change', function () {
                                document.querySelectorAll('.list-checkbox').forEach(function (cb) {
                                    cb.checked = selectAll.checked;
                                });
                            });
                        }
                    })();
                </script>
            @endif
        </div>
    </div>
</x-app-layout>
