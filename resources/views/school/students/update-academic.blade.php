<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Academic Info Update') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-64">
                    <select name="section_id" class="block w-full border-gray-300 rounded-md shadow-sm" required onchange="this.form.submit()">
                        <option value="">Select</option>
                        <option value="all" @selected($showAll)>All Sections</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>

        @if ($section || $showAll)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4"><input type="checkbox" id="select-all-students"></th>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Section</th>
                            <th class="py-3 px-4">Group</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-t">
                                <td class="py-2 px-4">
                                    <input type="checkbox" name="pick" class="student-pick"
                                           data-id="{{ $student->id }}"
                                           data-class="{{ $student->school_class_id }}"
                                           data-section="{{ $student->section_id }}"
                                           data-group="{{ $student->group_name }}"
                                           data-roll="{{ $student->roll }}">
                                </td>
                                <td class="py-2 px-4">{{ $student->admission_number }}</td>
                                <td class="py-2 px-4">{{ $student->name }}</td>
                                <td class="py-2 px-4">{{ $student->schoolClass?->name }}</td>
                                <td class="py-2 px-4">{{ $student->roll }}</td>
                                <td class="py-2 px-4">{{ $student->section?->name }}</td>
                                <td class="py-2 px-4">{{ $student->group_name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 text-center text-gray-400">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="edit-panel" class="bg-white shadow-sm rounded-lg p-6 hidden">
                <h3 class="font-semibold mb-1">Update Academic Info</h3>
                <p class="text-sm text-gray-500 mb-4"><span id="selected-count">0</span> student(s) selected</p>
                <form method="POST" action="{{ route('school.students.update-academic.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $showAll ? 'all' : $section->id }}">
                    <div id="f_student_ids"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Class" />
                            <select name="school_class_id" id="f_class" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Select</option>
                                @foreach ($classes as $classOption)
                                    <option value="{{ $classOption->id }}">{{ $classOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Section" />
                            <select name="new_section_id" id="f_section" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Select</option>
                                @foreach ($classes as $classOption)
                                    @foreach ($classOption->sections as $sectionOption)
                                        <option value="{{ $sectionOption->id }}" data-class="{{ $classOption->id }}">{{ $classOption->name }} - {{ $sectionOption->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Group" />
                            <select name="group_name" id="f_group" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="f_roll_wrap">
                            <x-input-label value="Roll" />
                            <x-text-input name="roll" id="f_roll" class="mt-1 block w-full" />
                            <p id="f_roll_hint" class="text-xs text-gray-400 mt-1 hidden">Roll is only applied when a single student is selected.</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-8 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            <script>
                var selectAll = document.getElementById('select-all-students');
                var picks = document.querySelectorAll('.student-pick');

                function syncPanel() {
                    var checked = Array.from(picks).filter(function (p) { return p.checked; });
                    var panel = document.getElementById('edit-panel');
                    var idsWrap = document.getElementById('f_student_ids');
                    var rollHint = document.getElementById('f_roll_hint');

                    idsWrap.innerHTML = '';
                    checked.forEach(function (p) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'student_ids[]';
                        input.value = p.dataset.id;
                        idsWrap.appendChild(input);
                    });

                    document.getElementById('selected-count').textContent = checked.length;

                    if (checked.length === 0) {
                        panel.classList.add('hidden');
                        return;
                    }

                    panel.classList.remove('hidden');

                    if (checked.length === 1) {
                        document.getElementById('f_class').value = checked[0].dataset.class || '';
                        document.getElementById('f_section').value = checked[0].dataset.section || '';
                        document.getElementById('f_group').value = checked[0].dataset.group || '';
                        document.getElementById('f_roll').value = checked[0].dataset.roll || '';
                        document.getElementById('f_roll').disabled = false;
                        rollHint.classList.add('hidden');
                    } else {
                        document.getElementById('f_roll').value = '';
                        document.getElementById('f_roll').disabled = true;
                        rollHint.classList.remove('hidden');
                    }

                    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                picks.forEach(function (checkbox) {
                    checkbox.addEventListener('change', syncPanel);
                });

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        picks.forEach(function (checkbox) { checkbox.checked = selectAll.checked; });
                        syncPanel();
                    });
                }
            </script>
        @endif
    </div>
</x-app-layout>
