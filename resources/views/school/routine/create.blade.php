<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Routine Period') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.routine.store') }}" class="space-y-4">
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
                        <select id="subject_id" name="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">—</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="teacher_id" value="Teacher" />
                        <select id="teacher_id" name="teacher_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">—</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="day_of_week" value="Day" />
                        <select id="day_of_week" name="day_of_week" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @foreach (\App\Models\RoutinePeriod::DAYS as $num => $name)
                                <option value="{{ $num }}" @selected(old('day_of_week') == $num)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="starts_at" value="Starts At" />
                            <x-text-input id="starts_at" type="time" name="starts_at" class="mt-1 block w-full" value="{{ old('starts_at') }}" required />
                        </div>
                        <div>
                            <x-input-label for="ends_at" value="Ends At" />
                            <x-text-input id="ends_at" type="time" name="ends_at" class="mt-1 block w-full" value="{{ old('ends_at') }}" required />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Add Period</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
