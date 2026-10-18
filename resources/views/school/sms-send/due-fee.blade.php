<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Send Due Fee SMS') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <x-input-label for="academic_year_id" value="Academic Year" />
                        <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Select --</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(request('academic_year_id') == $year->id)>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="shift_id" value="Shift" />
                        <select id="shift_id" name="shift_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Select --</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}" @selected(request('shift_id') == $shift->id)>{{ $shift->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>Search</x-primary-button>
                </form>
            </div>

            <form method="POST" action="{{ route('school.sms-send.due-fee.store') }}">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ request('academic_year_id') }}">
                <input type="hidden" name="shift_id" value="{{ request('shift_id') }}">

                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <x-input-label for="content" value="SMS Content" />
                    <textarea id="content" name="content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('content', $defaultContent) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Available tags: @{{student_name}}, @{{class}}, @{{due_amount}}</p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr class="text-gray-500">
                                <th class="py-2 px-4"><input type="checkbox" onclick="document.querySelectorAll('.fee-cb').forEach(c=>c.checked=this.checked)"></th>
                                <th class="py-2 px-4">ID</th>
                                <th class="py-2 px-4">Name</th>
                                <th class="py-2 px-4">Mobile No.</th>
                                <th class="py-2 px-4">Total Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dueStudents as $student)
                                <tr class="border-t">
                                    <td class="py-2 px-4"><input class="fee-cb" type="checkbox" name="student_ids[]" value="{{ $student->id }}"></td>
                                    <td class="py-2 px-4">{{ $student->id }}</td>
                                    <td class="py-2 px-4">{{ $student->name }}</td>
                                    <td class="py-2 px-4">{{ $student->guardian_phone }}</td>
                                    <td class="py-2 px-4">{{ number_format($student->due_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-4 px-4 text-center text-gray-400">No data. Select an academic year and shift above.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4 flex justify-end">
                        <x-primary-button>Send SMS</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
