@php($collapseId = 'import-panel-'.$mode)

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border import-accordion-item">
    <button type="button" class="w-full flex items-center justify-between px-6 py-4 font-semibold text-gray-800"
            onclick="
                document.querySelectorAll('.import-accordion-item').forEach(function (item) {
                    if (item !== this.closest('.import-accordion-item')) {
                        item.querySelector('.import-accordion-body').classList.add('hidden');
                        var icon = item.querySelector('.chevron-icon');
                        icon.classList.remove('bi-chevron-down');
                        icon.classList.add('bi-chevron-right');
                    }
                }.bind(this));
                document.getElementById('{{ $collapseId }}').classList.toggle('hidden');
                this.querySelector('.chevron-icon').classList.toggle('bi-chevron-down');
                this.querySelector('.chevron-icon').classList.toggle('bi-chevron-right');
            ">
        <span>{{ $title }}</span>
        <i class="bi chevron-icon {{ $collapseOpen ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
    </button>

    <div id="{{ $collapseId }}" class="import-accordion-body {{ $collapseOpen ? '' : 'hidden' }}">
        <div class="bg-gray-100 px-6 py-3 flex items-center justify-between border-t">
            <span class="font-semibold text-sm">Download Sample Excel File</span>
            <a href="{{ route('school.students.import.sample', $mode) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                <i class="bi bi-download"></i> Download Sample File
            </a>
        </div>

        @unless ($preview)
            <form method="POST" action="{{ route('school.students.import.preview', $mode) }}" enctype="multipart/form-data" class="px-6 py-4 space-y-4">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <x-input-label :for="'academic_year_id_'.$mode" value="Academic Year *" />
                        <select id="academic_year_id_{{ $mode }}" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select Academic Year</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :for="'section_id_'.$mode" value="Section *" />
                        <select id="section_id_{{ $mode }}" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select Section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->schoolClass?->name }} - {{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :for="'group_name_'.$mode" value="Group *" />
                        <select id="group_name_{{ $mode }}" name="group_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select Group</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :for="'category_'.$mode" value="Category *" />
                        <select id="category_{{ $mode }}" name="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-gray-100 -mx-6 px-6 py-4 flex flex-wrap items-center gap-4">
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="text-sm" required>
                    <x-primary-button>Upload File</x-primary-button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            @foreach ($columns as $column)
                                <th class="py-2 px-3">{{ $column }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="{{ count($columns) }}" class="py-8 text-center text-gray-400">No Data</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 flex justify-end border-t">
                <x-primary-button disabled class="opacity-50 cursor-not-allowed">Save</x-primary-button>
            </div>
        @else
            <div class="px-6 py-3 flex items-center justify-between border-t">
                <span class="text-sm text-gray-600">{{ count($preview['rows']) }} student(s) found. Review and click Save to import.</span>
                <form method="POST" action="{{ route('school.students.import.cancel', $mode) }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline text-sm">Cancel &amp; Re-upload</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            @foreach ($columns as $column)
                                <th class="py-2 px-3">{{ $column }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($preview['rows'] as $row)
                            <tr class="border-t">
                                @if ($mode === 'with-id')
                                    <td class="py-2 px-3">{{ $row['admission_number'] }}</td>
                                @endif
                                <td class="py-2 px-3">{{ $row['roll'] }}</td>
                                <td class="py-2 px-3">{{ $row['name'] }}</td>
                                <td class="py-2 px-3 capitalize">{{ $row['gender'] }}</td>
                                <td class="py-2 px-3">{{ $row['religion'] }}</td>
                                <td class="py-2 px-3">{{ $row['father_name'] }}</td>
                                <td class="py-2 px-3">{{ $row['mother_name'] }}</td>
                                <td class="py-2 px-3">{{ $row['guardian_phone'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($columns) }}" class="py-8 text-center text-gray-400">No data found in the uploaded file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('school.students.import.store', $mode) }}" class="px-6 py-4 flex justify-end border-t">
                @csrf
                <x-primary-button>Save</x-primary-button>
            </form>
        @endunless
    </div>
</div>
