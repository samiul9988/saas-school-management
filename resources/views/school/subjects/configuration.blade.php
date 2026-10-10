<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Subject Configuration') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <x-input-label for="school_class_id" value="Class" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required onchange="this.form.submit()">
                        <option value="">Select</option>
                        @foreach ($classes as $classOption)
                            <option value="{{ $classOption->id }}" @selected(request('school_class_id') == $classOption->id)>{{ $classOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="group_name" value="Group" />
                    <select id="group_name" name="group_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required onchange="this.form.submit()">
                        <option value="">Select</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group }}" @selected(request('group_name') === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if ($class && $groupName)
                <form method="POST" action="{{ route('school.exam-configuration.subject.store') }}">
                    @csrf
                    <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                    <input type="hidden" name="group_name" value="{{ $groupName }}">

                    <x-input-label value="Select Subject(s)" />
                    <div class="mt-2 border rounded-md p-4 grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-3">
                        @foreach ($subjects as $subject)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" @checked(in_array($subject->id, $assignedSubjectIds))>
                                {{ $subject->name }}
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4 flex justify-center">
                        <button type="submit" class="px-10 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                            Save
                        </button>
                    </div>
                </form>
            @endif
        </div>

        @if ($class && $groupName && $assignments->isNotEmpty())
            <form method="POST" action="{{ route('school.exam-configuration.subject.update') }}" class="bg-white shadow-sm rounded-lg overflow-hidden">
                @csrf
                @method('PUT')
                <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                <input type="hidden" name="group_name" value="{{ $groupName }}">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Subject Type</th>
                            <th class="py-3 px-4">Subject Serial</th>
                            <th class="py-3 px-4">Merge ID</th>
                            <th class="py-3 px-4">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignments as $assignment)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $assignment->subject?->name }}</td>
                                <td class="py-2 px-4">
                                    <select name="assignments[{{ $assignment->id }}][subject_type]" class="border-gray-300 rounded-md shadow-sm text-sm">
                                        @foreach ($subjectTypes as $type)
                                            <option value="{{ $type }}" @selected($assignment->subject_type === $type)>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2 px-4">
                                    <input type="number" name="assignments[{{ $assignment->id }}][serial]" value="{{ $assignment->serial }}" min="0" class="w-24 border-gray-300 rounded-md shadow-sm text-sm">
                                </td>
                                <td class="py-2 px-4">
                                    <input type="number" name="assignments[{{ $assignment->id }}][merge_id]" value="{{ $assignment->merge_id }}" min="0" class="w-24 border-gray-300 rounded-md shadow-sm text-sm">
                                </td>
                                <td class="py-2 px-4">
                                    <button type="submit" form="delete-assignment-{{ $assignment->id }}" class="text-red-600 hover:text-red-800" onclick="return confirm('Remove this subject?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                        Update
                    </button>
                </div>
            </form>

            @foreach ($assignments as $assignment)
                <form id="delete-assignment-{{ $assignment->id }}" action="{{ route('school.exam-configuration.subject.destroy', $assignment) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        @endif
    </div>
</x-app-layout>
