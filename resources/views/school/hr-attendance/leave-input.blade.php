<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Leave Input') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('school.hr-attendance.leave-input') }}" class="flex flex-wrap items-end gap-4 mb-6">
                <div>
                    <x-input-label value="Staff / Teacher ID" />
                    <x-text-input type="text" name="teacher_search" value="{{ request('teacher_search') }}" class="mt-1 block" placeholder="Enter ID" />
                </div>
                <button type="submit" class="px-6 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                    Search
                </button>
            </form>

            @if ($teacher)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div>
                        <x-input-label value="Staff / Teacher ID" />
                        <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->staff_serial ?? $teacher->id }}" disabled />
                    </div>
                    <div>
                        <x-input-label value="Staff / Teacher Name" />
                        <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->name }}" disabled />
                    </div>
                    <div>
                        <x-input-label value="Designation" />
                        <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $teacher->designation }}" disabled />
                    </div>
                </div>

                <div class="border rounded-lg overflow-hidden mb-6">
                    <div class="bg-blue-500 text-white font-semibold px-4 py-2">Leave Application</div>
                    <form method="POST" action="{{ route('school.hr-attendance.leave-input.store') }}" class="p-4 grid grid-cols-1 sm:grid-cols-4 gap-4">
                        @csrf
                        <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                        <div>
                            <x-input-label value="From" />
                            <x-text-input type="date" name="from_date" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label value="To" />
                            <x-text-input type="date" name="to_date" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label value="Leave Reason" />
                            <select name="leave_reason" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Select Reason</option>
                                @foreach ($reasons as $reason)
                                    <option value="{{ $reason }}">{{ $reason }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Leave For" />
                            <x-text-input type="text" name="leave_for" class="mt-1 block w-full" />
                        </div>
                        <div class="sm:col-span-4">
                            <button type="submit" class="px-6 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="overflow-x-auto border rounded-md">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Leave Date</th>
                            <th class="py-3 px-4">Leave Reason</th>
                            <th class="py-3 px-4">Leave For</th>
                            <th class="py-3 px-4">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaves as $leave)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $leave->date->toDateString() }}</td>
                                <td class="py-2 px-4">{{ $leave->leave_reason }}</td>
                                <td class="py-2 px-4">{{ $leave->leave_for }}</td>
                                <td class="py-2 px-4">
                                    <form action="{{ route('school.hr-attendance.leave-input.destroy', $leave) }}" method="POST" onsubmit="return confirm('Remove this leave day?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-400">No Data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
