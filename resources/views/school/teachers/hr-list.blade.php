<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Basic Info') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Designation</th>
                        <th class="py-3 px-4">Gender</th>
                        <th class="py-3 px-4">Religion</th>
                        <th class="py-3 px-4">Mobile</th>
                        <th class="py-3 px-4">Blood Group</th>
                        <th class="py-3 px-4">Image</th>
                        <th class="py-3 px-4">View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $teacher->staff_serial ?: $teacher->id }}</td>
                            <td class="py-2 px-4">{{ $teacher->name }}</td>
                            <td class="py-2 px-4">{{ $teacher->designation }}</td>
                            <td class="py-2 px-4">{{ $teacher->gender ? ucfirst($teacher->gender) : '' }}</td>
                            <td class="py-2 px-4">{{ $teacher->religion }}</td>
                            <td class="py-2 px-4">{{ $teacher->phone }}</td>
                            <td class="py-2 px-4">{{ $teacher->blood_group }}</td>
                            <td class="py-2 px-4">
                                <div class="w-9 h-9 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center">
                                    @if ($teacher->photo)
                                        <img src="{{ asset('storage/'.$teacher->photo) }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <i class="bi bi-person text-gray-400"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-4">
                                <button type="button" class="border rounded px-2 py-1 text-gray-600 hover:bg-gray-50" data-bs-toggle="modal" data-bs-target="#view-modal-{{ $teacher->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="py-6 text-center text-gray-400">No staff found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($teachers as $teacher)
        <div class="modal fade" id="view-modal-{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Staff / Teacher Info</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body space-y-4">
                        <div class="flex gap-4 items-start">
                            <div class="w-20 h-20 rounded bg-gray-100 overflow-hidden flex items-center justify-center flex-shrink-0">
                                @if ($teacher->photo)
                                    <img src="{{ asset('storage/'.$teacher->photo) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <i class="bi bi-person text-3xl text-gray-400"></i>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-4 flex-1">
                                <div>
                                    <x-input-label value="Staff / Teacher Name" />
                                    <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->name }}" disabled />
                                </div>
                                <div>
                                    <x-input-label value="ID" />
                                    <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->staff_serial ?: $teacher->id }}" disabled />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Employment Date" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->joining_date?->format('d-M-Y') }}" disabled />
                            </div>
                            <div>
                                <x-input-label value="Resignation Date" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->resignation_date?->format('d-M-Y') }}" disabled />
                            </div>
                            <div>
                                <x-input-label value="Designation" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->designation }}" disabled />
                            </div>
                        </div>

                        <div class="font-semibold pt-2 border-t">Basic Information</div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Father's Name" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->father_name }}" disabled />
                            </div>
                            <div>
                                <x-input-label value="Mother's Name" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->mother_name }}" disabled />
                            </div>
                            <div>
                                <x-input-label value="Date Of Birth" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->date_of_birth?->format('d-M-Y') }}" disabled />
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Gender" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->gender ? ucfirst($teacher->gender) : '' }}" disabled />
                            </div>
                            <div>
                                <x-input-label value="Religion" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->religion }}" disabled />
                            </div>
                            <div>
                                <x-input-label value="Blood Group" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->blood_group }}" disabled />
                            </div>
                        </div>

                        <div class="font-semibold pt-2 border-t">Address Information</div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Mobile Number" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->phone }}" disabled />
                            </div>
                            <div>
                                <x-input-label value="Email" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->email }}" disabled />
                            </div>
                            <div>
                                <x-input-label value="Staff Serial" />
                                <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->staff_serial }}" disabled />
                            </div>
                        </div>
                        <div>
                            <x-input-label value="Staff Address" />
                            <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50" rows="2" disabled>{{ $teacher->address }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
