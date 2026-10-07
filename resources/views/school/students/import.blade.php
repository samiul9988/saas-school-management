<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Multiple Student') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Download Sample Excel File</h3>
                    <a href="{{ route('school.students.import.sample') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                        <i class="bi bi-download"></i> Download Sample File
                    </a>
                </div>
                <p class="text-sm text-gray-500">
                    Columns required: <strong>Name, Roll No, Father's Name, Mother's Name, Gender, Religion, Mobile No.</strong>
                </p>
            </div>

            @unless ($preview)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-4">Student Excel Registration</h3>
                    <form method="POST" action="{{ route('school.students.import.preview') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="academic_year_id" value="Academic Year" />
                                <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">—</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="school_class_id" value="Class" />
                                <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">— Select Class —</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('school_class_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="section_id" value="Section" />
                                <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">—</option>
                                    @foreach ($classes as $class)
                                        @foreach ($class->sections as $section)
                                            <option value="{{ $section->id }}">{{ $class->name }} - {{ $section->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="group_name" value="Group" />
                                <select id="group_name" name="group_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">—</option>
                                    @foreach (['Science', 'Commerce', 'Arts', 'General'] as $group)
                                        <option value="{{ $group }}">{{ $group }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <x-input-label for="file" value="Choose Excel File" />
                                <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" class="mt-1 block w-full text-sm" required>
                                <x-input-error :messages="$errors->get('file')" class="mt-2" />
                            </div>
                            <x-primary-button>Upload File</x-primary-button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold">Preview ({{ count($preview['rows']) }} student(s) found)</h3>
                        <form method="POST" action="{{ route('school.students.import.cancel') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:underline text-sm">Cancel &amp; Re-upload</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50">
                                <tr class="text-gray-500">
                                    <th class="py-2 px-3">Roll No</th>
                                    <th class="py-2 px-3">Name</th>
                                    <th class="py-2 px-3">Father's Name</th>
                                    <th class="py-2 px-3">Mother's Name</th>
                                    <th class="py-2 px-3">Gender</th>
                                    <th class="py-2 px-3">Religion</th>
                                    <th class="py-2 px-3">Mobile No.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($preview['rows'] as $row)
                                    <tr class="border-t">
                                        <td class="py-2 px-3">{{ $row['roll'] }}</td>
                                        <td class="py-2 px-3">{{ $row['name'] }}</td>
                                        <td class="py-2 px-3">{{ $row['father_name'] }}</td>
                                        <td class="py-2 px-3">{{ $row['mother_name'] }}</td>
                                        <td class="py-2 px-3 capitalize">{{ $row['gender'] }}</td>
                                        <td class="py-2 px-3">{{ $row['religion'] }}</td>
                                        <td class="py-2 px-3">{{ $row['guardian_phone'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-6 text-center text-gray-400">No data found in the uploaded file.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <form method="POST" action="{{ route('school.students.import.store') }}" class="mt-4 flex justify-end">
                        @csrf
                        <x-primary-button>Save</x-primary-button>
                    </form>
                </div>
            @endunless
        </div>
    </div>
</x-app-layout>
