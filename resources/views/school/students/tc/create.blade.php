<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Transfer Certificate') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="flex gap-1 px-4 pt-2 border-b">
                <a href="{{ route('school.students.tc.create') }}" class="px-4 py-2 text-sm rounded-t-md border border-b-0 text-indigo-600 font-semibold bg-white">
                    Generate Transfer Certificate
                </a>
                <a href="{{ route('school.students.tc.index') }}" class="px-4 py-2 text-sm rounded-t-md text-gray-500 hover:text-gray-700">
                    All Transfer Certificate
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
            <div class="px-6 py-4 border-b font-semibold">Generate Transfer Certificate</div>
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
            <form method="POST" action="{{ route('school.students.tc.store') }}" class="bg-white shadow-sm rounded-lg p-6">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label value="Student Name" />
                        <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $student->name }}" disabled />
                    </div>
                    <div>
                        <x-input-label value="Address" />
                        <x-text-input name="address" class="mt-1 block w-full" value="{{ old('address', $certificate?->address ?? $student->address) }}" placeholder="N/A" />
                    </div>
                    <div>
                        <x-input-label value="Class" />
                        <x-text-input name="class_name" class="mt-1 block w-full" value="{{ old('class_name', $certificate?->class_name ?? $student->schoolClass?->name) }}" />
                    </div>

                    <div>
                        <x-input-label value="Roll" />
                        <x-text-input name="roll" class="mt-1 block w-full" value="{{ old('roll', $certificate?->roll ?? $student->roll) }}" />
                    </div>
                    <div>
                        <x-input-label value="GPA" />
                        <x-text-input name="gpa" class="mt-1 block w-full" value="{{ old('gpa', $certificate?->gpa ?? '') }}" placeholder="GPA" />
                    </div>
                    <div>
                        <x-input-label value="Registration Number" />
                        <x-text-input name="registration_number" class="mt-1 block w-full" value="{{ old('registration_number', $certificate?->registration_number ?? '') }}" placeholder="Registration Number" />
                    </div>

                    <div>
                        <x-input-label value="Cause Of Leaving" />
                        <select name="cause_of_leaving" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($causes as $cause)
                                <option value="{{ $cause }}" @selected(old('cause_of_leaving', $certificate?->cause_of_leaving ?? 'Others') === $cause)>{{ $cause }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Examination Name" />
                        <x-text-input name="examination_name" class="mt-1 block w-full" value="{{ old('examination_name', $certificate?->examination_name ?? '') }}" placeholder="N/A" />
                    </div>
                    <div>
                        <x-input-label value="Examination Date" />
                        <x-text-input type="date" name="examination_date" class="mt-1 block w-full" value="{{ old('examination_date', $certificate?->examination_date?->format('Y-m-d')) }}" />
                    </div>

                    <div>
                        <x-input-label value="Father's Name" />
                        <x-text-input name="father_name" class="mt-1 block w-full" value="{{ old('father_name', $certificate?->father_name ?? $student->father_name) }}" />
                    </div>
                    <div>
                        <x-input-label value="Mother's Name" />
                        <x-text-input name="mother_name" class="mt-1 block w-full" value="{{ old('mother_name', $certificate?->mother_name ?? $student->mother_name) }}" />
                    </div>
                    <div>
                        <x-input-label value="Session" />
                        <x-text-input name="session" class="mt-1 block w-full" value="{{ old('session', $certificate?->session ?? '') }}" placeholder="Session" />
                    </div>

                    <div>
                        <x-input-label value="Date of Birth" />
                        <x-text-input type="date" name="date_of_birth" class="mt-1 block w-full" value="{{ old('date_of_birth', ($certificate?->date_of_birth ?? $student->date_of_birth)?->format('Y-m-d')) }}" />
                    </div>
                    <div>
                        <x-input-label value="Gender" />
                        <select name="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="son" @selected(old('gender', $certificate?->gender ?? ($student->gender === 'female' ? 'daughter' : 'son')) === 'son')>Son</option>
                            <option value="daughter" @selected(old('gender', $certificate?->gender ?? ($student->gender === 'female' ? 'daughter' : 'son')) === 'daughter')>Daughter</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Promoted Class" />
                        <x-text-input name="promoted_class" class="mt-1 block w-full" value="{{ old('promoted_class', $certificate?->promoted_class ?? '') }}" placeholder="N/A" />
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
