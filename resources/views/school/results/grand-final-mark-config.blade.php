<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Grand Final Mark Configuration') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <a href="{{ route('school.exam-configuration.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Back to Exam Configuration</a>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Class Name</th>
                        <th class="py-3 px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $class)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $class->name }}</td>
                            <td class="py-2 px-4">
                                <button type="button" class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-semibold hover:bg-gray-50" data-bs-toggle="modal" data-bs-target="#class-modal-{{ $class->id }}">
                                    Update
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="py-6 text-center text-gray-400">No classes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($classes as $class)
        @php($existingComponents = $class->grandFinal?->components ?? collect())
        <div class="modal fade" id="class-modal-{{ $class->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form action="{{ route('school.exam-configuration.grand-final.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title">Mark Configuration</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="w-full text-sm text-left mb-4">
                                <thead class="bg-blue-100">
                                    <tr class="text-gray-700">
                                        <th class="py-2 px-3">Serial</th>
                                        <th class="py-2 px-3">Exam Name</th>
                                        <th class="py-2 px-3">Percentage(%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($row = 0; $row < 3; $row++)
                                        @php($component = $existingComponents->get($row))
                                        <tr class="border-t">
                                            <td class="py-2 px-3">{{ $row + 1 }}</td>
                                            <td class="py-2 px-3">
                                                <select name="exam_ids[]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                                    <option value="">Select</option>
                                                    @foreach ($exams as $exam)
                                                        <option value="{{ $exam->id }}" @selected($component?->exam_id === $exam->id)>{{ $exam->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="py-2 px-3">
                                                <input type="number" step="0.01" min="0" max="100" name="percentages[]"
                                                       value="{{ $component?->weight }}"
                                                       class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>

                            <x-input-label :for="'merit_process_type_class_'.$class->id" value="Select Process No." />
                            <select id="merit_process_type_class_{{ $class->id }}" name="merit_process_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Select</option>
                                @foreach ($meritTypes as $value => $label)
                                    <option value="{{ $value }}" @selected($class->grandFinal?->merit_process_type === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
