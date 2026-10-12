<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Testimonial') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="flex gap-1 px-4 pt-2 border-b">
                <a href="{{ route('school.students.testimonial.create') }}" class="px-4 py-2 text-sm rounded-t-md border border-b-0 text-indigo-600 font-semibold bg-white">
                    Generate Testimonial
                </a>
                <a href="{{ route('school.students.testimonial.index') }}" class="px-4 py-2 text-sm rounded-t-md text-gray-500 hover:text-gray-700">
                    All Testimonial
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b font-semibold">Generate Testimonial</div>
            <div class="p-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="w-64">
                        <x-input-label value="Student ID" />
                        <x-text-input name="student_id" class="mt-1 block w-full" value="{{ request('student_id') }}" />
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                        <i class="bi bi-search"></i> Search
                    </button>
                </form>
            </div>
        </div>

        @if ($student)
            <form method="POST" action="{{ route('school.students.testimonial.store') }}" class="bg-white shadow-sm rounded-lg p-6">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label value="Student Name" />
                        <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $student->name }}" disabled />
                    </div>
                    <div>
                        <x-input-label value="Address" />
                        <x-text-input name="address" class="mt-1 block w-full" value="{{ old('address', $testimonial?->address ?? $student->address) }}" placeholder="N/A" />
                    </div>
                    <div>
                        <x-input-label value="Class" />
                        <x-text-input name="class_name" class="mt-1 block w-full" value="{{ old('class_name', $testimonial?->class_name ?? $student->schoolClass?->name) }}" />
                    </div>

                    <div>
                        <x-input-label value="Roll" />
                        <x-text-input name="roll" class="mt-1 block w-full" value="{{ old('roll', $testimonial?->roll ?? $student->roll) }}" />
                    </div>
                    <div>
                        <x-input-label value="GPA" />
                        <x-text-input name="gpa" class="mt-1 block w-full" value="{{ old('gpa', $testimonial?->gpa ?? '') }}" placeholder="GPA" />
                    </div>
                    <div>
                        <x-input-label value="Registration Number" />
                        <x-text-input name="registration_number" class="mt-1 block w-full" value="{{ old('registration_number', $testimonial?->registration_number ?? '') }}" placeholder="Registration Number" />
                    </div>

                    <div>
                        <x-input-label value="Father's Name" />
                        <x-text-input name="father_name" class="mt-1 block w-full" value="{{ old('father_name', $testimonial?->father_name ?? $student->father_name) }}" />
                    </div>
                    <div>
                        <x-input-label value="Mother's Name" />
                        <x-text-input name="mother_name" class="mt-1 block w-full" value="{{ old('mother_name', $testimonial?->mother_name ?? $student->mother_name) }}" />
                    </div>
                    <div>
                        <x-input-label value="Session" />
                        <x-text-input name="session" class="mt-1 block w-full" value="{{ old('session', $testimonial?->session ?? '') }}" placeholder="Session" />
                    </div>

                    <div>
                        <x-input-label value="Date of Birth" />
                        <x-text-input type="date" name="date_of_birth" class="mt-1 block w-full" value="{{ old('date_of_birth', ($testimonial?->date_of_birth ?? $student->date_of_birth)?->format('Y-m-d')) }}" />
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-8 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700">
                        Submit
                    </button>
                </div>
            </form>
        @elseif (request()->filled('student_id'))
            <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-400">No student found with that ID.</div>
        @endif
    </div>
</x-app-layout>
