<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Assign Subject') }} &mdash; {{ $exam->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.exams.subjects.store', $exam) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">— Select class —</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('school_class_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="subject_id" value="Subject" />
                        <select id="subject_id" name="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">— Select subject —</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('subject_id')" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="full_marks" value="Full Marks" />
                            <x-text-input id="full_marks" type="number" step="0.01" name="full_marks" class="mt-1 block w-full" value="{{ old('full_marks', 100) }}" required />
                        </div>
                        <div>
                            <x-input-label for="pass_marks" value="Pass Marks" />
                            <x-text-input id="pass_marks" type="number" step="0.01" name="pass_marks" class="mt-1 block w-full" value="{{ old('pass_marks', 33) }}" required />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="exam_date" value="Exam Date" />
                        <x-text-input id="exam_date" type="date" name="exam_date" class="mt-1 block w-full" value="{{ old('exam_date') }}" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Assign</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
