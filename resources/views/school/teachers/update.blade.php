<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Basic Info') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
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
                                <button type="button" class="border rounded px-2 py-1 text-gray-600 hover:bg-gray-50" data-bs-toggle="modal" data-bs-target="#edit-modal-{{ $teacher->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-6 text-center text-gray-400">No staff found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($teachers as $teacher)
        <div class="modal fade" id="edit-modal-{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('school.teachers.update', $teacher) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="{{ $teacher->status }}">

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
                                        <x-text-input name="name" class="mt-1 block w-full" value="{{ $teacher->name }}" required />
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
                                    <x-text-input type="date" name="joining_date" class="mt-1 block w-full" value="{{ $teacher->joining_date?->format('Y-m-d') }}" />
                                </div>
                                <div>
                                    <x-input-label value="Resignation Date" />
                                    <x-text-input type="date" name="resignation_date" class="mt-1 block w-full" value="{{ $teacher->resignation_date?->format('Y-m-d') }}" />
                                </div>
                                <div>
                                    <x-input-label value="Designation" />
                                    <x-text-input name="designation" class="mt-1 block w-full" value="{{ $teacher->designation }}" />
                                </div>
                            </div>

                            <div class="font-semibold pt-2 border-t">Basic Information</div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <x-input-label value="Father's Name" />
                                    <x-text-input name="father_name" class="mt-1 block w-full" value="{{ $teacher->father_name }}" />
                                </div>
                                <div>
                                    <x-input-label value="Mother's Name" />
                                    <x-text-input name="mother_name" class="mt-1 block w-full" value="{{ $teacher->mother_name }}" />
                                </div>
                                <div>
                                    <x-input-label value="Date Of Birth" />
                                    <x-text-input type="date" name="date_of_birth" class="mt-1 block w-full" value="{{ $teacher->date_of_birth?->format('Y-m-d') }}" />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <x-input-label value="Gender" />
                                    <select name="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select</option>
                                        <option value="male" @selected($teacher->gender === 'male')>Male</option>
                                        <option value="female" @selected($teacher->gender === 'female')>Female</option>
                                        <option value="other" @selected($teacher->gender === 'other')>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label value="Religion" />
                                    <select name="religion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select</option>
                                        @foreach (['Islam', 'Hinduism', 'Christianity', 'Buddhism', 'Other'] as $religion)
                                            <option value="{{ $religion }}" @selected($teacher->religion === $religion)>{{ $religion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label value="Blood Group" />
                                    <select name="blood_group" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select</option>
                                        @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                            <option value="{{ $group }}" @selected($teacher->blood_group === $group)>{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="font-semibold pt-2 border-t">Address Information</div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <x-input-label value="Mobile Number" />
                                    <x-text-input name="phone" class="mt-1 block w-full" value="{{ $teacher->phone }}" />
                                </div>
                                <div>
                                    <x-input-label value="Email" />
                                    <x-text-input type="email" name="email" class="mt-1 block w-full" value="{{ $teacher->email }}" />
                                </div>
                                <div>
                                    <x-input-label value="Staff Serial" />
                                    <x-text-input name="staff_serial" class="mt-1 block w-full" value="{{ $teacher->staff_serial }}" />
                                </div>
                            </div>
                            <div>
                                <x-input-label value="Staff Address" />
                                <textarea name="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ $teacher->address }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
