<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Initial '.$meta['label'].' Setup') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
            <div class="flex flex-wrap gap-2">
                @foreach ($types as $key => $meta2)
                    <a href="{{ route('school.students.initial-setup', $key) }}"
                       class="px-4 py-2 rounded-md text-sm font-semibold {{ $type === $key ? 'bg-[#2196f3] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        {{ $meta2['label'] }}
                    </a>
                @endforeach
            </div>

            <form method="POST" action="{{ route('school.students.initial-setup.store', $type) }}" class="flex flex-wrap gap-4 items-end">
                @csrf
                @if ($meta['needsClass'])
                    <div class="w-56">
                        <select name="school_class_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select Class</option>
                            @foreach ($classes as $classOption)
                                <option value="{{ $classOption->id }}">{{ $classOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="w-64">
                    <input type="text" name="name" class="block w-full border-gray-300 rounded-md shadow-sm" placeholder="{{ $meta['placeholder'] }}" required>
                </div>
                <div class="w-40">
                    <input type="number" name="serial" class="block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter Serial" min="0">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                    <i class="bi bi-save"></i> Save
                </button>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Serial</th>
                        @if ($meta['needsClass'])
                            <th class="py-3 px-4">Class</th>
                        @endif
                        <th class="py-3 px-4">{{ $meta['label'] }}</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Edit</th>
                        <th class="py-3 px-4">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $record->serial }}</td>
                            @if ($meta['needsClass'])
                                <td class="py-2 px-4">{{ $record->schoolClass?->name }}</td>
                            @endif
                            <td class="py-2 px-4">{{ $record->name }}</td>
                            <td class="py-2 px-4">
                                <form method="POST" action="{{ route('school.students.initial-setup.toggle', [$type, $record->id]) }}">
                                    @csrf
                                    <button type="submit" class="{{ $record->status === 'enable' ? 'text-green-600' : 'text-red-600' }} font-semibold hover:underline">
                                        {{ ucfirst($record->status) }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-2 px-4">
                                <button type="button" class="text-blue-600 hover:text-blue-800" data-bs-toggle="modal" data-bs-target="#edit-modal-{{ $record->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                            <td class="py-2 px-4">
                                <form action="{{ route('school.students.initial-setup.destroy', [$type, $record->id]) }}" method="POST" onsubmit="return confirm('Remove this {{ strtolower($meta['label']) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-400">No {{ strtolower($meta['label']) }} records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($records as $record)
        <div class="modal fade" id="edit-modal-{{ $record->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('school.students.initial-setup.update', [$type, $record->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $meta['label'] }} Update</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body space-y-4">
                            @if ($meta['needsClass'])
                                <div>
                                    <x-input-label value="Class" />
                                    <select name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                        @foreach ($classes as $classOption)
                                            <option value="{{ $classOption->id }}" @selected($record->school_class_id == $classOption->id)>{{ $classOption->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div>
                                <x-input-label :value="$meta['label'].':'" />
                                <x-text-input name="name" class="mt-1 block w-full" value="{{ $record->name }}" required />
                            </div>
                            <div>
                                <x-input-label value="Serial:" />
                                <x-text-input type="number" name="serial" class="mt-1 block w-full" value="{{ $record->serial }}" min="0" />
                            </div>
                            <div class="flex items-center gap-2">
                                <x-input-label value="Enable/Disable:" />
                                <input type="checkbox" name="status" value="1" @checked($record->status === 'enable')>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
