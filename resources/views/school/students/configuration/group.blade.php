<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Group Configuration') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
            <form method="POST" action="{{ route('school.students.configuration.group.store') }}" class="bg-white shadow-sm rounded-lg p-4 space-y-3">
                @csrf
                <select name="school_class_id" id="class-select" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                    <option value="">Select Class</option>
                    @foreach ($classes as $classOption)
                        <option value="{{ $classOption->id }}" @selected((string) $selectedClassId === (string) $classOption->id)>{{ $classOption->name }}</option>
                    @endforeach
                </select>

                <div id="group-count" class="text-sm text-gray-500">Select Groups( {{ count($selectedGroupIds) }} item selected)</div>

                <div class="space-y-2 max-h-[420px] overflow-y-auto">
                    @foreach ($groups as $group)
                        <label class="block">
                            <input type="checkbox" name="group_ids[]" value="{{ $group->id }}" class="hidden peer group-checkbox" @checked(in_array($group->id, $selectedGroupIds))>
                            <span class="block text-center px-4 py-2 rounded-md text-sm font-medium cursor-pointer bg-gray-100 text-gray-700 peer-checked:bg-[#2196f3] peer-checked:text-white hover:bg-gray-200">
                                {{ $group->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="w-full px-4 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    Save
                </button>
            </form>

            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('school.students.configuration.group.update') }}">
                    @csrf
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-blue-100">
                                <tr class="text-gray-700">
                                    <th class="py-3 px-4">Serial</th>
                                    <th class="py-3 px-4">Class</th>
                                    <th class="py-3 px-4">Group</th>
                                    <th class="py-3 px-4">Application Fee</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assignments as $assignment)
                                    <tr class="border-t">
                                        <td class="py-2 px-4">
                                            <input type="number" name="serials[{{ $assignment->id }}]" value="{{ $assignment->serial }}" min="0" class="w-20 border-gray-300 rounded-md shadow-sm text-sm">
                                        </td>
                                        <td class="py-2 px-4">{{ $assignment->schoolClass?->name }}</td>
                                        <td class="py-2 px-4">{{ $assignment->group?->name }}</td>
                                        <td class="py-2 px-4">
                                            <input type="number" name="fees[{{ $assignment->id }}]" value="{{ $assignment->application_fee }}" step="0.01" min="0" class="w-24 border-gray-300 rounded-md shadow-sm text-sm">
                                        </td>
                                        <td class="py-2 px-4">
                                            <button type="submit" formaction="{{ route('school.students.configuration.group.toggle', $assignment) }}" class="{{ $assignment->status === 'enable' ? 'text-green-600' : 'text-red-600' }} font-semibold hover:underline">
                                                {{ $assignment->status === 'enable' ? 'Enabled' : 'Disabled' }}
                                            </button>
                                        </td>
                                        <td class="py-2 px-4">
                                            <button type="submit" formaction="{{ route('school.students.configuration.group.destroy', $assignment) }}" onclick="return confirm('Remove this group assignment?')" class="text-red-600 hover:text-red-800">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-6 text-center text-gray-400">No group configurations yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($assignments->isNotEmpty())
                        <div class="flex justify-end mt-4">
                            <button type="submit" formaction="{{ route('school.students.configuration.group.update') }}" class="px-6 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                                Update
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <form id="class-switch-form" method="GET" action="{{ route('school.students.configuration.group') }}">
        <input type="hidden" name="school_class_id" id="class-switch-input">
    </form>

    <script>
        (function () {
            var classSelect = document.getElementById('class-select');
            var switchInput = document.getElementById('class-switch-input');
            var countLabel = document.getElementById('group-count');
            var checkboxes = document.querySelectorAll('.group-checkbox');

            classSelect.addEventListener('change', function () {
                switchInput.value = classSelect.value;
                document.getElementById('class-switch-form').submit();
            });

            function refreshCount() {
                var count = Array.from(checkboxes).filter(function (cb) { return cb.checked; }).length;
                countLabel.textContent = 'Select Groups( ' + count + ' item selected)';
            }
            checkboxes.forEach(function (cb) { cb.addEventListener('change', refreshCount); });
        })();
    </script>
</x-app-layout>
