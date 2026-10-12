<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Year Change With Merit Position') }}</h2>
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
                    <x-input-label value="Section" />
                    <select name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required onchange="this.form.submit()">
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Exam" />
                    <select name="result_batch_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($resultBatches as $batch)
                            <option value="{{ $batch->id }}" @selected((string) $resultBatchId === (string) $batch->id)>{{ $batch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Merit Position" />
                    <select name="merit_position" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach ($meritPositions as $position)
                            <option value="{{ $position }}">{{ $position }}</option>
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

        @if ($section && $resultBatchId)
            <form method="POST" action="{{ route('school.students.migration-with-merit.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="section_id" value="{{ $section->id }}">
                <input type="hidden" name="result_batch_id" value="{{ $resultBatchId }}">

                <div class="text-right text-sm text-gray-500">Total Found {{ $rows->count() }}</div>

                <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-3 px-4"><input type="checkbox" id="select-all"></th>
                                <th class="py-3 px-4">Position</th>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Class</th>
                                <th class="py-3 px-4">Roll</th>
                                <th class="py-3 px-4">GPA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="border-t">
                                    <td class="py-2 px-4"><input type="checkbox" class="student-checkbox" name="student_ids[]" value="{{ $row->student_id }}"></td>
                                    <td class="py-2 px-4">{{ $row->position }}</td>
                                    <td class="py-2 px-4">{{ $row->student?->admission_number }}</td>
                                    <td class="py-2 px-4">{{ $row->student?->name }}</td>
                                    <td class="py-2 px-4">{{ $row->student?->schoolClass?->name }}</td>
                                    <td class="py-2 px-4"><input type="text" name="rolls[{{ $row->student_id }}]" value="{{ $row->student?->roll }}" class="w-20 border-gray-300 rounded-md shadow-sm text-sm"></td>
                                    <td class="py-2 px-4">{{ $row->gpa }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-6 text-center text-gray-400">No results found for this exam/section.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($rows->isNotEmpty())
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
