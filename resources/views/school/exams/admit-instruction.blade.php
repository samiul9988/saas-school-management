<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Admit Card Instruction') }}</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b font-semibold">Admit Card Instruction</div>
        <div class="p-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
            @endif

            <form method="GET" class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <x-input-label for="school_class_id" value="Class" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()" required>
                        <option value="">Select</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="exam_id" value="Exam" />
                    <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()" required>
                        <option value="">Select</option>
                        @foreach ($exams as $exam)
                            <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if ($instruction)
                <form method="POST" action="{{ route('school.exams.admit-instruction.store') }}" class="grid grid-cols-2 gap-4">
                    @csrf
                    <input type="hidden" name="school_class_id" value="{{ request('school_class_id') }}">
                    <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">

                    <div>
                        <x-input-label for="instruction_1" value="Instruction - 1" />
                        <textarea id="instruction_1" name="instruction_1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ old('instruction_1', $instruction->instruction_1) }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="instruction_2" value="Instruction - 2" />
                        <textarea id="instruction_2" name="instruction_2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ old('instruction_2', $instruction->instruction_2) }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="instruction_3" value="Instruction - 3" />
                        <textarea id="instruction_3" name="instruction_3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ old('instruction_3', $instruction->instruction_3) }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="instruction_4" value="Instruction - 4" />
                        <textarea id="instruction_4" name="instruction_4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ old('instruction_4', $instruction->instruction_4) }}</textarea>
                    </div>

                    <div class="col-span-2 flex justify-end">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
