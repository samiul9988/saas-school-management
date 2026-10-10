<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Exam Configuration') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <a href="{{ route('school.results-process.grand-final') }}" class="text-indigo-600 hover:underline text-sm">&larr; Back to Grand Final Process</a>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="POST" action="{{ route('school.exam-configuration.store') }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($classes as $classOption)
                                <option value="{{ $classOption->id }}" @selected(old('school_class_id') == $classOption->id)>{{ $classOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="merit_process_type" value="Merit Process Type" />
                        <select id="merit_process_type" name="merit_process_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select</option>
                            @foreach ($meritTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('merit_process_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <x-input-label value="Select Exam(s)" />
                    <div class="mt-2 border rounded-md p-4 grid grid-cols-1 sm:grid-cols-4 gap-x-6 gap-y-3 max-h-80 overflow-y-auto">
                        @foreach ($exams as $exam)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="exam_ids[]" value="{{ $exam->id }}"
                                       @checked(in_array($exam->id, old('exam_ids', [])))>
                                {{ $exam->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <x-primary-button>Save</x-primary-button>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Class</th>
                        <th class="py-3 px-4">Exam name</th>
                        <th class="py-3 px-4">Merit Process Type</th>
                        <th class="py-3 px-4">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grandFinals as $grandFinal)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $grandFinal->schoolClass?->name }}</td>
                            <td class="py-2 px-4">{{ $grandFinal->components->pluck('exam.name')->filter()->join(', ') }}</td>
                            <td class="py-2 px-4">{{ $meritTypes[$grandFinal->merit_process_type] ?? '—' }}</td>
                            <td class="py-2 px-4 space-x-2">
                                <button type="button" class="text-indigo-600 hover:underline" data-bs-toggle="modal" data-bs-target="#edit-modal-{{ $grandFinal->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <form action="{{ route('school.exam-configuration.destroy', $grandFinal) }}" method="POST" class="inline" onsubmit="return confirm('Delete this Grand Final?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">No Grand Finals created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($grandFinals as $grandFinal)
        <div class="modal fade" id="edit-modal-{{ $grandFinal->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('school.exam-configuration.update-merit-type', $grandFinal) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Exam Configuration Update</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body space-y-4">
                            <div>
                                <x-input-label value="Class Name" />
                                <input type="text" value="{{ $grandFinal->schoolClass?->name }}" disabled class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500">
                            </div>
                            <div>
                                <x-input-label value="Exam Name" />
                                <input type="text" value="{{ $grandFinal->components->pluck('exam.name')->filter()->join(', ') }}" disabled class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500">
                            </div>
                            <div>
                                <x-input-label :for="'merit_process_type_'.$grandFinal->id" value="Merit Position Type" />
                                <select id="merit_process_type_{{ $grandFinal->id }}" name="merit_process_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    @foreach ($meritTypes as $value => $label)
                                        <option value="{{ $value }}" @selected($grandFinal->merit_process_type === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
