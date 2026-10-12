<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Class Configuration') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="POST" action="{{ route('school.students.configuration.class.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                @csrf
                <div>
                    <x-input-label value="Class:" />
                    <select name="school_class_id" id="f_class" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($classes as $classOption)
                            <option value="{{ $classOption->id }}">{{ $classOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Shift:" />
                    <select name="shift_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">N/A</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Section:" />
                    <select name="section_id" id="f_section" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($classes as $classOption)
                            @foreach ($classOption->sections as $sectionOption)
                                <option value="{{ $sectionOption->id }}" data-class="{{ $classOption->id }}">{{ $classOption->name }} - {{ $sectionOption->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full px-6 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                        <i class="bi bi-save"></i> Save
                    </button>
                </div>
            </form>
        </div>

        <form method="POST" action="{{ route('school.students.configuration.class.update-serial') }}">
            @csrf
            <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Serial</th>
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Shift</th>
                            <th class="py-3 px-4">Section</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sections as $section)
                            <tr class="border-t">
                                <td class="py-2 px-4">
                                    <input type="number" name="serials[{{ $section->id }}]" value="{{ $section->serial }}" min="0" class="w-24 border-gray-300 rounded-md shadow-sm text-sm">
                                </td>
                                <td class="py-2 px-4">{{ $section->schoolClass?->name }}</td>
                                <td class="py-2 px-4">{{ $section->shift?->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4">{{ $section->name }}</td>
                                <td class="py-2 px-4">
                                    <button type="submit" formaction="{{ route('school.students.configuration.class.toggle', $section) }}" class="{{ $section->status === 'enable' ? 'text-green-600' : 'text-red-600' }} font-semibold hover:underline">
                                        {{ ucfirst($section->status) }}
                                    </button>
                                </td>
                                <td class="py-2 px-4">
                                    <button type="submit" formaction="{{ route('school.students.configuration.class.destroy', $section) }}" onclick="return confirm('Remove this shift assignment?')" class="text-red-600 hover:text-red-800">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-400">No sections found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center gap-2">
                    {{ $sections->links() }}
                </div>
                <button type="submit" formaction="{{ route('school.students.configuration.class.update-serial') }}" class="px-6 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                    Update Serial
                </button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            var classSelect = document.getElementById('f_class');
            var sectionSelect = document.getElementById('f_section');
            classSelect.addEventListener('change', function () {
                Array.from(sectionSelect.options).forEach(function (opt) {
                    if (!opt.value) return;
                    opt.hidden = opt.dataset.class && opt.dataset.class !== classSelect.value;
                });
                sectionSelect.value = '';
            });
        })();
    </script>
</x-app-layout>
