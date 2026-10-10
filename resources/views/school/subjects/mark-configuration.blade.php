<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Mark Configuration') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end mb-6">
                <div>
                    <x-input-label for="school_class_id" value="Class" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($classes as $classOption)
                            <option value="{{ $classOption->id }}" @selected(request('school_class_id') == $classOption->id)>{{ $classOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="group_name" value="Group" />
                    <select id="group_name" name="group_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group }}" @selected(request('group_name') === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button>
                    <i class="bi bi-search mr-1"></i> Search
                </x-primary-button>
            </form>

            @if ($class && $groupName)
                <form method="POST" action="{{ route('school.exam-configuration.mark.store') }}" id="mark-config-form">
                    @csrf
                    <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                    <input type="hidden" name="group_name" value="{{ $groupName }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label value="Step 1 : Select Subject(s)" />
                            <div class="mt-2 border rounded-md p-3 max-h-56 overflow-y-auto space-y-1">
                                @forelse ($availableSubjects as $subject)
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="subject_id[]" value="{{ $subject->id }}">
                                        {{ $subject->name }}
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-400">No subjects configured for this class/group yet.</p>
                                @endforelse
                            </div>
                        </div>
                        <div>
                            <x-input-label value="Step 2 : Select Exam(s)" />
                            <div class="mt-2 border rounded-md p-3 max-h-56 overflow-y-auto space-y-1">
                                @forelse ($exams as $exam)
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="exam_id[]" value="{{ $exam->id }}">
                                        {{ $exam->name }}
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-400">No exams found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <x-input-label value="Step 3 : Mark Configure" />
                    <div class="overflow-hidden border rounded-md mt-2">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-blue-100">
                                <tr class="text-gray-700">
                                    <th class="py-2 px-3">Title</th>
                                    <th class="py-2 px-3">Total Mark</th>
                                    <th class="py-2 px-3">Pass Mark</th>
                                    <th class="py-2 px-3">Acceptance</th>
                                    <th class="py-2 px-3">Delete</th>
                                </tr>
                            </thead>
                            <tbody id="step3-rows">
                                @forelse ($shortCodes as $shortCode)
                                    <tr class="border-t">
                                        <td class="py-2 px-3">
                                            <input type="hidden" name="exam_short_code_id[]" value="{{ $shortCode->id }}">
                                            {{ $shortCode->name }}
                                        </td>
                                        <td class="py-2 px-3"><input type="number" step="0.01" min="0" name="total[]" value="{{ $shortCode->prefill_total }}" class="w-24 border-gray-300 rounded-md shadow-sm text-sm"></td>
                                        <td class="py-2 px-3"><input type="number" step="0.01" min="0" name="pass_mark[]" value="{{ $shortCode->prefill_pass_mark }}" class="w-24 border-gray-300 rounded-md shadow-sm text-sm"></td>
                                        <td class="py-2 px-3"><input type="number" step="0.01" min="0" name="acceptance[]" value="{{ $shortCode->prefill_acceptance }}" class="w-24 border-gray-300 rounded-md shadow-sm text-sm"></td>
                                        <td class="py-2 px-3"><button type="button" class="text-red-600 hover:text-red-800 remove-row"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-6 text-center text-gray-400">No short codes configured yet. Add them from Exams &rarr; Settings.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            @endif
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end mb-6">
                @if ($class)
                    <input type="hidden" name="school_class_id" value="{{ request('school_class_id') }}">
                    <input type="hidden" name="group_name" value="{{ request('group_name') }}">
                @endif
                <div>
                    <x-input-label for="view_school_class_id" value="Class" />
                    <select id="view_school_class_id" name="view_school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($classes as $classOption)
                            <option value="{{ $classOption->id }}" @selected(request('view_school_class_id') == $classOption->id)>{{ $classOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="view_group_name" value="Group" />
                    <select id="view_group_name" name="view_group_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group }}" @selected(request('view_group_name') === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="view_exam_id" value="Exam" />
                    <select id="view_exam_id" name="view_exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($exams as $exam)
                            <option value="{{ $exam->id }}" @selected(request('view_exam_id') == $exam->id)>{{ $exam->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button>
                    <i class="bi bi-search mr-1"></i> Search
                </x-primary-button>
            </form>

            @if ($viewClass)
                <div class="overflow-hidden border rounded-md">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-2 px-3">Subject</th>
                                <th class="py-2 px-3">Short Code</th>
                                <th class="py-2 px-3">Total Mark</th>
                                <th class="py-2 px-3">Pass Mark</th>
                                <th class="py-2 px-3">Acceptance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($existingRows as $row)
                                <tr class="border-t">
                                    <td class="py-2 px-3">{{ $row->subject?->name }}</td>
                                    <td class="py-2 px-3">{{ $row->shortCode?->name }}</td>
                                    <td class="py-2 px-3">{{ rtrim(rtrim(number_format($row->total, 2), '0'), '.') }}</td>
                                    <td class="py-2 px-3">{{ rtrim(rtrim(number_format($row->pass_mark, 2), '0'), '.') }}</td>
                                    <td class="py-2 px-3">{{ rtrim(rtrim(number_format($row->acceptance, 2), '0'), '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-center text-gray-400">No Data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('step3-rows')?.addEventListener('click', function (e) {
            var btn = e.target.closest('.remove-row');
            if (! btn) return;
            btn.closest('tr').remove();
        });
    </script>
</x-app-layout>
