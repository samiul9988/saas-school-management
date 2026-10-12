<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Enable / Disable') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="flex gap-3">
            <a href="{{ route('school.students.enable-disable', array_merge(request()->only('section_id'), ['mode' => 'disable'])) }}"
               class="px-6 py-2 rounded-md font-semibold text-sm {{ $mode === 'disable' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                To Disable
            </a>
            <a href="{{ route('school.students.enable-disable', array_merge(request()->only('section_id'), ['mode' => 'enable'])) }}"
               class="px-6 py-2 rounded-md font-semibold text-sm {{ $mode === 'enable' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                To Enable
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b font-semibold">Student {{ ucfirst($mode) }}</div>
            <div class="p-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    <div class="w-64">
                        <select name="section_id" class="block w-full border-gray-300 rounded-md shadow-sm" required onchange="this.form.submit()">
                            <option value="">Select</option>
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
        </div>

        @if ($section)
            <form method="POST" action="{{ route('school.students.enable-disable.store') }}">
                @csrf
                <input type="hidden" name="section_id" value="{{ $section->id }}">
                <input type="hidden" name="mode" value="{{ $mode }}">

                <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-3 px-4"><input type="checkbox" id="select-all"></th>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Roll</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Gender</th>
                                <th class="py-3 px-4">Mobile No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr class="border-t">
                                    <td class="py-2 px-4"><input type="checkbox" class="student-checkbox" name="student_ids[]" value="{{ $student->id }}"></td>
                                    <td class="py-2 px-4">{{ $student->admission_number }}</td>
                                    <td class="py-2 px-4">{{ $student->roll }}</td>
                                    <td class="py-2 px-4">{{ $student->name }}</td>
                                    <td class="py-2 px-4">{{ ucfirst($student->gender) }}</td>
                                    <td class="py-2 px-4">{{ $student->guardian_phone }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-400">No Data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($students->isNotEmpty())
                    <div class="mt-4">
                        <button type="submit" class="px-10 py-2.5 border border-transparent rounded-md font-semibold text-sm text-white {{ $mode === 'disable' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                            {{ ucfirst($mode) }}
                        </button>
                    </div>
                @endif
            </form>

            <script>
                var selectAll = document.getElementById('select-all');
                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        document.querySelectorAll('.student-checkbox').forEach(function (cb) { cb.checked = selectAll.checked; });
                    });
                }
            </script>
        @endif
    </div>
</x-app-layout>
