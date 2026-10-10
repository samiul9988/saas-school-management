<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Grand Final Mark Sheet Download') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
                <div>
                    <x-input-label for="section_id" value="Section" />
                    <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required onchange="this.form.submit()">
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}" @selected(request('section_id') == $sectionOption->id)>
                                {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="from_roll" value="From Roll" />
                    <x-text-input id="from_roll" type="number" name="from_roll" class="mt-1 block w-full" value="{{ request('from_roll', 0) }}" />
                </div>
                <div>
                    <x-input-label for="to_roll" value="To Roll" />
                    <x-text-input id="to_roll" type="number" name="to_roll" class="mt-1 block w-full" value="{{ request('to_roll', 0) }}" />
                </div>

                @forelse ($grandFinals as $grandFinal)
                    <div>
                        <a href="{{ $section ? route('school.mark-sheets.grand-final.download', [
                                'resultBatch' => $grandFinal,
                                'section_id' => request('section_id'),
                                'from_roll' => request('from_roll'),
                                'to_roll' => request('to_roll'),
                            ]) : '#' }}"
                           @unless($section) onclick="event.preventDefault(); alert('Please select a Section first.');" @endunless
                           class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600">
                            <i class="bi bi-download mr-2"></i> {{ $grandFinal->components->count() }} Exam Marksheet
                        </a>
                    </div>
                @empty
                    <div class="text-sm text-gray-400 sm:col-span-2">No Grand Finals have been created yet. Create one from Result Process &rarr; Grand Final.</div>
                @endforelse
            </form>
        </div>
    </div>
</x-app-layout>
