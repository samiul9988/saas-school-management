<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Photo Update') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-64">
                    <select name="section_id" class="block w-full border-gray-300 rounded-md shadow-sm" required onchange="this.form.submit()">
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>

        @if ($section)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Photo</th>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-t">
                                <td class="py-2 px-4">
                                    <div class="w-10 h-10 rounded-md bg-gray-100 overflow-hidden flex items-center justify-center">
                                        @if ($student->photo)
                                            <img src="{{ asset('storage/'.$student->photo) }}" class="w-full h-full object-cover" alt="">
                                        @else
                                            <i class="bi bi-person text-gray-400"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-2 px-4">{{ $student->admission_number }}</td>
                                <td class="py-2 px-4">{{ $student->name }}</td>
                                <td class="py-2 px-4">{{ $student->roll }}</td>
                                <td class="py-2 px-4">
                                    <form method="POST" action="{{ route('school.students.update-photo.store') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="section_id" value="{{ $section->id }}">
                                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                                        <input type="file" name="photo" accept="image/*" class="text-xs" required>
                                        <button type="submit" class="text-xs px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700">Upload</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-gray-400">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
