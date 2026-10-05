<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Attendance') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                            <option value="">— Select class —</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($sections->isNotEmpty())
                        <div>
                            <x-input-label for="section_id" value="Section" />
                            <select id="section_id" name="section_id" class="mt-1 border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                                <option value="">All sections</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="date" type="date" name="date" class="mt-1" value="{{ $date }}" onchange="this.form.submit()" />
                    </div>
                </form>
            </div>

            @if ($students->isNotEmpty())
                <form method="POST" action="{{ route('school.attendance.store') }}" class="bg-white shadow-sm sm:rounded-lg p-6">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}" />
                    <input type="hidden" name="school_class_id" value="{{ request('school_class_id') }}" />
                    <input type="hidden" name="section_id" value="{{ request('section_id') }}" />

                    <table class="w-full text-sm text-left mb-4">
                        <thead>
                            <tr class="text-gray-500 border-b">
                                <th class="py-2">Roll</th>
                                <th class="py-2">Name</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr class="border-b">
                                    <td class="py-2">{{ $student->roll ?? '—' }}</td>
                                    <td class="py-2">{{ $student->name }}</td>
                                    <td class="py-2">
                                        <select name="statuses[{{ $student->id }}]" class="border-gray-300 rounded-md shadow-sm text-sm">
                                            @foreach (['present', 'absent', 'late', 'leave'] as $status)
                                                <option value="{{ $status }}" @selected($student->today_status === $status)>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <x-primary-button>Save Attendance</x-primary-button>
                </form>
            @elseif (request('school_class_id'))
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-400">No active students found.</div>
            @endif
        </div>
    </div>
</x-app-layout>
