@php($activeTab = request('tab', 'download'))
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Admit & Seat Plan') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="border-b flex gap-1 px-4 pt-2">
                <a href="{{ route('school.exams.seat-plan', ['tab' => 'download']) }}"
                   class="px-4 py-2 text-sm rounded-t-md {{ $activeTab === 'download' ? 'bg-white border border-b-0 text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                    Download Admit Card
                </a>
                <a href="{{ route('school.exams.seat-plan', ['tab' => 'template']) }}"
                   class="px-4 py-2 text-sm rounded-t-md {{ $activeTab === 'template' ? 'bg-white border border-b-0 text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                    Select Admit Card Template
                </a>
            </div>

            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 mx-6 mt-4 rounded">{{ session('status') }}</div>
            @endif

            @if ($activeTab === 'download')
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
            @else
                <div class="px-6 py-4 border-b font-semibold">Select Admit Card Template</div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach (range(1, 4) as $i)
                        <div class="border rounded-lg overflow-hidden {{ $school->admit_card_template == $i ? 'ring-2 ring-indigo-500' : '' }}">
                            <div class="p-3 font-semibold text-center">Template {{ $i }}</div>
                            <div class="px-3">
                                @include("school.exams.admit-card-previews.template-{$i}")
                            </div>
                            <form action="{{ route('school.exams.seat-plan.select-template') }}" method="POST" class="p-3">
                                @csrf
                                <input type="hidden" name="template" value="{{ $i }}">
                                <button type="submit" class="w-full {{ $school->admit_card_template == $i ? 'bg-green-600' : 'bg-indigo-600' }} text-white py-2 rounded hover:opacity-90">
                                    {{ $school->admit_card_template == $i ? 'Selected' : 'Take' }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
