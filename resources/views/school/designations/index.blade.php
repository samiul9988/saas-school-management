<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Designation') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-[#eef2f6] rounded-lg p-4">
            <form method="POST" action="{{ route('school.designations.store') }}" class="flex flex-wrap gap-3 items-center">
                @csrf
                <input type="text" name="name" class="border-gray-300 rounded-md shadow-sm" placeholder="Enter Name!" required>
                <input type="number" name="serial" class="border-gray-300 rounded-md shadow-sm w-40" placeholder="Enter Serial" min="0">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md font-semibold text-sm hover:bg-green-700">
                    <i class="bi bi-save"></i> Save
                </button>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Serial</th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Edit</th>
                        <th class="py-3 px-4">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($designations as $designation)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $designation->serial }}</td>
                            <td class="py-2 px-4">{{ $designation->name }}</td>
                            <td class="py-2 px-4">
                                <span class="font-semibold {{ $designation->status === 'enable' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $designation->status === 'enable' ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="py-2 px-4">
                                <button type="button" class="text-blue-600 hover:text-blue-800" data-bs-toggle="modal" data-bs-target="#edit-modal-{{ $designation->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                            <td class="py-2 px-4">
                                <form method="POST" action="{{ route('school.designations.destroy', $designation) }}" onsubmit="return confirm('Delete this designation?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-400">No designations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($designations as $designation)
        <div class="modal fade" id="edit-modal-{{ $designation->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('school.designations.update', $designation) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Update Designation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body space-y-3">
                            <input type="text" name="name" class="block w-full border-gray-300 rounded-md shadow-sm" value="{{ $designation->name }}" required>
                            <input type="number" name="serial" class="block w-full border-gray-300 rounded-md shadow-sm" value="{{ $designation->serial }}" min="0">
                            <label class="flex items-center gap-2 text-sm">
                                Enable/Disable:
                                <input type="checkbox" name="status" value="1" @checked($designation->status === 'enable')>
                            </label>
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md font-semibold text-sm hover:bg-green-700">
                                <i class="bi bi-save"></i> Update
                            </button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
