<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Send Attendance SMS') }}</h2>
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
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" value="{{ request('date') }}" required />
                    </div>
                    <div>
                        <x-input-label for="section_id" value="Section" />
                        <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Select --</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="period_id" value="Period" />
                        <select id="period_id" name="period_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Select --</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}" @selected(request('period_id') == $period->id)>{{ $period->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>Search</x-primary-button>
                </form>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Total Student</p><p class="text-2xl font-bold">{{ $summary['total'] }}</p></div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Present</p><p class="text-2xl font-bold text-green-600">{{ $summary['present'] }}</p></div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4"><p class="text-sm text-gray-500">Absent</p><p class="text-2xl font-bold text-red-600">{{ $summary['absent'] }}</p></div>
            </div>

            <form method="POST" action="{{ route('school.sms-send.attendance.store') }}">
                @csrf
                <input type="hidden" name="date" value="{{ request('date') }}">
                <input type="hidden" name="section_id" value="{{ request('section_id') }}">
                <input type="hidden" name="period_id" value="{{ request('period_id') }}">

                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6 space-y-4">
                    <div>
                        <x-input-label value="Select SMS For" />
                        <div class="mt-1 flex gap-6">
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="target" value="both" checked> Both</label>
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="target" value="present"> Present</label>
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="target" value="absent"> Absent</label>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="content" value="SMS Content" />
                        <textarea id="content" name="content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('content', $defaultContent) }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Available tags: @{{student_name}}, @{{status}}, @{{date}}</p>
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Send SMS</x-primary-button>
                    </div>
                </div>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-4">Student</th>
                            <th class="py-2 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $record->student?->name }}</td>
                                <td class="py-2 px-4 capitalize">{{ $record->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-4 px-4 text-center text-gray-400">No data. Select date, section and period above.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
