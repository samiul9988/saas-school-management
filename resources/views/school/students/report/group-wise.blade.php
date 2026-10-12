<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Group Wise Student List') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-64">
                    <select name="school_class_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($classes as $classOption)
                            <option value="{{ $classOption->id }}" @selected($class && $class->id === $classOption->id)>{{ $classOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-64">
                    <select name="group_name" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group }}" @selected($groupName === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>

        @if ($class && $groupName)
            <div class="text-right text-sm text-gray-500">Total Student Found <span class="font-semibold">{{ $students->count() }}</span></div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Group</th>
                            <th class="py-3 px-4">Father</th>
                            <th class="py-3 px-4">Mother</th>
                            <th class="py-3 px-4">Gender</th>
                            <th class="py-3 px-4">Religion</th>
                            <th class="py-3 px-4">Mobile</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $student->admission_number }}</td>
                                <td class="py-2 px-4">{{ $student->name }}</td>
                                <td class="py-2 px-4">{{ $student->roll }}</td>
                                <td class="py-2 px-4">{{ $student->group_name }}</td>
                                <td class="py-2 px-4">{{ $student->father_name }}</td>
                                <td class="py-2 px-4">{{ $student->mother_name }}</td>
                                <td class="py-2 px-4">{{ ucfirst((string) $student->gender) }}</td>
                                <td class="py-2 px-4">{{ $student->religion }}</td>
                                <td class="py-2 px-4">{{ $student->guardian_phone }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="py-6 text-center text-gray-400">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($students->isNotEmpty())
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('school.students.report.group-wise.pdf', ['school_class_id' => $class->id, 'group_name' => $groupName]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2196f3] text-white rounded-md font-semibold text-sm hover:bg-blue-600">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <a href="{{ route('school.students.report.group-wise.excel', ['school_class_id' => $class->id, 'group_name' => $groupName]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2196f3] text-white rounded-md font-semibold text-sm hover:bg-blue-600">
                        <i class="bi bi-download"></i> Download Excel
                    </a>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
