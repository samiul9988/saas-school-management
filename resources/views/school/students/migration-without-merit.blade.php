<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Year Change Without Merit Position') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label value="Academic Year" />
                    <select name="target_academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}" @selected((string) request('target_academic_year_id') === (string) $year->id)>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Section" />
                    <select name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Group" />
                    <select name="group_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
        </div>

        @if ($section)
            <form method="POST" action="{{ route('school.students.migration-without-merit.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="section_id" value="{{ $section->id }}">
                <input type="hidden" name="group_name" value="{{ $groupName }}">
                <input type="hidden" name="target_academic_year_id" value="{{ request('target_academic_year_id') }}">

                <div class="text-right text-sm text-gray-500">Total Found {{ $students->count() }}</div>

                <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-3 px-4"><input type="checkbox" id="select-all"></th>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Class</th>
                                <th class="py-3 px-4">Roll</th>
                                <th class="py-3 px-4">Section</th>
                                <th class="py-3 px-4">Group</th>
                                <th class="py-3 px-4">Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr class="border-t">
                                    <td class="py-2 px-4"><input type="checkbox" class="student-checkbox" name="student_ids[]" value="{{ $student->id }}"></td>
                                    <td class="py-2 px-4">{{ $student->admission_number }}</td>
                                    <td class="py-2 px-4">{{ $student->name }}</td>
                                    <td class="py-2 px-4">{{ $student->schoolClass?->name }}</td>
                                    <td class="py-2 px-4"><input type="text" name="rolls[{{ $student->id }}]" value="{{ $student->roll }}" class="w-20 border-gray-300 rounded-md shadow-sm text-sm"></td>
                                    <td class="py-2 px-4">{{ $student->section?->name }}</td>
                                    <td class="py-2 px-4">{{ $student->group_name }}</td>
                                    <td class="py-2 px-4">{{ $student->category }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="py-6 text-center text-gray-400">No students found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($students->isNotEmpty())
                    <div>
                        <button type="submit" id="update-btn" class="px-10 py-2.5 bg-gray-300 text-gray-500 border border-transparent rounded-md font-semibold text-sm cursor-not-allowed" disabled>
                            Update
                        </button>
                    </div>
                @endif
            </form>

            <script>
                (function () {
                    var selectAll = document.getElementById('select-all');
                    var checkboxes = document.querySelectorAll('.student-checkbox');
                    var updateBtn = document.getElementById('update-btn');

                    function refreshButton() {
                        if (!updateBtn) return;
                        var anyChecked = Array.from(checkboxes).some(function (cb) { return cb.checked; });
                        updateBtn.disabled = !anyChecked;
                        updateBtn.classList.toggle('bg-gray-300', !anyChecked);
                        updateBtn.classList.toggle('text-gray-500', !anyChecked);
                        updateBtn.classList.toggle('cursor-not-allowed', !anyChecked);
                        updateBtn.classList.toggle('bg-green-600', anyChecked);
                        updateBtn.classList.toggle('text-white', anyChecked);
                        updateBtn.classList.toggle('hover:bg-green-700', anyChecked);
                    }

                    if (selectAll) {
                        selectAll.addEventListener('change', function () {
                            checkboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
                            refreshButton();
                        });
                    }
                    checkboxes.forEach(function (cb) { cb.addEventListener('change', refreshButton); });
                })();
            </script>
        @endif
    </div>
</x-app-layout>
