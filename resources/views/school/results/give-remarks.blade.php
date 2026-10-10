<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Exam Remarks Template') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
            <form action="{{ route('school.results-process.remark-templates.store') }}" method="POST" class="max-w-md flex gap-1">
                @csrf
                <input type="text" name="name" placeholder="Add a new remark to the list, e.g. CONGRATULATIONS…" class="block w-full text-xs border-gray-300 rounded-md shadow-sm">
                <button type="submit" class="text-xs bg-gray-800 text-white px-3 rounded hover:bg-gray-700 whitespace-nowrap">Add to Remarks List</button>
            </form>

            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="remarks_template" value="Remarks List" />
                        <select id="remarks_template" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                onchange="document.getElementById('remarks_text').value = this.value">
                            <option value="">Select</option>
                            @foreach ($remarkTemplates as $template)
                                <option value="{{ $template->name }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
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
                        <x-input-label for="result_batch_id" value="Exam" />
                        <select id="result_batch_id" name="result_batch_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($batches as $batch)
                                <option value="{{ $batch->id }}" @selected(request('result_batch_id') == $batch->id)>
                                    {{ $batch->type === 'grand_final' ? 'Grand Final: ' : '' }}{{ $batch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <x-input-label for="remarks_text" value="Remarks" />
                    <textarea id="remarks_text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="2" placeholder="Type a remark or pick one from Remarks List above…"></textarea>
                </div>

                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search mr-1"></i> Search
                </button>
            </form>
        </div>

        @if ($resultBatch && $section)
            <form method="POST" action="{{ route('school.results-process.give-remarks.store') }}" class="bg-white shadow-sm rounded-lg overflow-hidden" id="remarks-form">
                @csrf
                <input type="hidden" name="result_batch_id" value="{{ $resultBatch->id }}">
                <input type="hidden" name="section_id" value="{{ $section->id }}">
                <input type="hidden" name="remarks" id="remarks_hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4"><input type="checkbox" id="select-all"></th>
                            <th class="py-3 px-4">Student ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Total Marks</th>
                            <th class="py-3 px-4">Section Position</th>
                            <th class="py-3 px-4">Grade</th>
                            <th class="py-3 px-4">CGPA</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-t">
                                <td class="py-2 px-4"><input type="checkbox" name="student_ids[]" value="{{ $row->student_id }}" class="row-check"></td>
                                <td class="py-2 px-4">{{ $row->student->id }}</td>
                                <td class="py-2 px-4">{{ $row->student->name }}</td>
                                <td class="py-2 px-4">{{ $row->student->roll }}</td>
                                <td class="py-2 px-4">{{ $row->total_obtained }} / {{ $row->total_full }}</td>
                                <td class="py-2 px-4">{{ $row->position ?? '—' }}</td>
                                <td class="py-2 px-4">{{ $row->grade?->name ?? '—' }}</td>
                                <td class="py-2 px-4">{{ $row->gpa }}</td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $row->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $row->is_passed ? 'Pass' : 'Fail' }}
                                    </span>
                                    @if ($row->remarks)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $row->remarks }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="py-8 text-center text-gray-400">No Data</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($rows->isNotEmpty())
                    <div class="p-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600">
                            Save
                        </button>
                    </div>
                @endif
            </form>

            <script>
                document.getElementById('select-all')?.addEventListener('change', function () {
                    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
                });

                document.getElementById('remarks-form')?.addEventListener('submit', function () {
                    document.getElementById('remarks_hidden').value = document.getElementById('remarks_text').value;
                });
            </script>
        @endif
    </div>
</x-app-layout>
