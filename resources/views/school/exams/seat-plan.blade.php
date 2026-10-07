<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Admit & Seat Plan') }}</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b font-semibold">Download Admit Card</div>
        <div class="p-6">
            <form method="GET" id="seat-plan-form" class="grid grid-cols-2 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="section_id" value="Section *" />
                    <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>{{ $section->schoolClass?->name }} - {{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="exam_id" value="Exam *" />
                    <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($exams as $exam)
                            <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" formaction="{{ route('school.exams.seat-plan.admit-card') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        <i class="bi bi-download"></i> Download Admit Card
                    </button>
                </div>

                <div>
                    <x-input-label for="from_roll" value="From Roll" />
                    <x-text-input id="from_roll" type="number" name="from_roll" class="mt-1 block w-full" value="{{ request('from_roll', 0) }}" />
                </div>
                <div>
                    <x-input-label for="to_roll" value="To Roll" />
                    <x-text-input id="to_roll" type="number" name="to_roll" class="mt-1 block w-full" value="{{ request('to_roll', 0) }}" />
                </div>

                <div class="sm:col-span-4 flex flex-wrap gap-3">
                    <button type="submit" formaction="{{ route('school.exams.seat-plan.seats', 10) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        <i class="bi bi-download"></i> Download Seat Plan (10 plan per page)
                    </button>
                    <button type="submit" formaction="{{ route('school.exams.seat-plan.seats', 4) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        <i class="bi bi-download"></i> Download Seat Plan (4 plan per page)
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
