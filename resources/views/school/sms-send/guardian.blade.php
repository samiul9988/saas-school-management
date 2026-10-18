<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Send SMS to Guardian') }}</h2>
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
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Select Class --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="section_id" value="Section (optional)" />
                        <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Select Section --</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>{{ $section->schoolClass?->name }} - {{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>Search</x-primary-button>
                </form>
            </div>

            <form method="POST" action="{{ route('school.sms-send.guardian.store') }}">
                @csrf
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <x-input-label for="content" value="SMS Content" />
                    <textarea id="content" name="content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('content', $defaultContent) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Available tags: @{{student_name}}, @{{roll}}, @{{class}}, @{{section}}, @{{father_name}}, @{{guardian_name}}</p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr class="text-gray-500">
                                <th class="py-2 px-4"><input type="checkbox" onclick="document.querySelectorAll('.student-cb').forEach(c=>c.checked=this.checked)"></th>
                                <th class="py-2 px-4">Student ID</th>
                                <th class="py-2 px-4">Student Name</th>
                                <th class="py-2 px-4">Roll</th>
                                <th class="py-2 px-4">Father's Name</th>
                                <th class="py-2 px-4">Mobile No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr class="border-t">
                                    <td class="py-2 px-4"><input class="student-cb" type="checkbox" name="student_ids[]" value="{{ $student->id }}"></td>
                                    <td class="py-2 px-4">{{ $student->id }}</td>
                                    <td class="py-2 px-4">{{ $student->name }}</td>
                                    <td class="py-2 px-4">{{ $student->roll }}</td>
                                    <td class="py-2 px-4">{{ $student->father_name }}</td>
                                    <td class="py-2 px-4">{{ $student->guardian_phone }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-4 px-4 text-center text-gray-400">No data. Select a class or section above.</td></tr>
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
