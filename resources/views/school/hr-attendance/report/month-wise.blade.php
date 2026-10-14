<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Attendance Report (Month Wise)') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex gap-2 mb-6">
                <button type="button" id="tab-btn-single" onclick="switchTab('single')"
                        class="px-6 py-2.5 rounded-md font-semibold text-sm bg-[#2196f3] text-white">Single Employee</button>
                <button type="button" id="tab-btn-excel" onclick="switchTab('excel')"
                        class="px-6 py-2.5 rounded-md font-semibold text-sm bg-gray-100 text-gray-700">All Employee Excel</button>
            </div>

            <div id="tab-single">
                <form method="GET" action="{{ route('school.hr-attendance.report.month-wise') }}" class="flex flex-wrap items-end gap-4 mb-6">
                    <div>
                        <x-input-label value="Select Year" />
                        <select name="year" class="mt-1 block border-gray-300 rounded-md shadow-sm">
                            @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Select Month" />
                        <select name="month" class="mt-1 block border-gray-300 rounded-md shadow-sm">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Select Employee" />
                        <select name="teacher_id" class="mt-1 block border-gray-300 rounded-md shadow-sm">
                            <option value="">Select</option>
                            @foreach ($teachers as $teacherOption)
                                <option value="{{ $teacherOption->id }}" @selected($teacher && $teacher->id == $teacherOption->id)>
                                    {{ $teacherOption->staff_serial ?? $teacherOption->id }}. {{ $teacherOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                        Search
                    </button>
                </form>

                @if ($teacher)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div class="text-sm space-y-1">
                            <div><strong>ID:</strong> {{ $teacher->staff_serial ?? $teacher->id }}</div>
                            <div><strong>Name:</strong> {{ $teacher->name }}</div>
                            <div><strong>Designation:</strong> {{ $teacher->designation }}</div>
                            <div><strong>Phone:</strong> {{ $teacher->phone }}</div>
                            <div><strong>Month:</strong> {{ \Carbon\Carbon::create()->month($month)->format('F') }}</div>
                            <div><strong>Year:</strong> {{ $year }}</div>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto border rounded-md">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Designation</th>
                                <th class="py-3 px-4">In Time</th>
                                <th class="py-3 px-4">In Time Status</th>
                                <th class="py-3 px-4">Out Time</th>
                                <th class="py-3 px-4">Out Time Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $record)
                                <tr class="border-t">
                                    <td class="py-2 px-4">{{ $record->date->toDateString() }}</td>
                                    <td class="py-2 px-4">{{ $teacher?->designation }}</td>
                                    <td class="py-2 px-4">{{ $record->in_time }}</td>
                                    <td class="py-2 px-4">{{ $record->in_status ? ucfirst(str_replace('_', ' ', $record->in_status)) : '--' }}</td>
                                    <td class="py-2 px-4">{{ $record->out_time }}</td>
                                    <td class="py-2 px-4">{{ $record->out_status ? ucfirst(str_replace('_', ' ', $record->out_status)) : '--' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-400">No Data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-excel" style="display: none;">
                <form method="GET" action="{{ route('school.hr-attendance.report.month-wise.excel') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <x-input-label value="Select Year" />
                        <select name="year" class="mt-1 block border-gray-300 rounded-md shadow-sm">
                            @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Select Month" />
                        <select name="month" class="mt-1 block border-gray-300 rounded-md shadow-sm">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                        Download
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.getElementById('tab-single').style.display = tab === 'single' ? '' : 'none';
            document.getElementById('tab-excel').style.display = tab === 'excel' ? '' : 'none';
            document.getElementById('tab-btn-single').className = 'px-6 py-2.5 rounded-md font-semibold text-sm ' + (tab === 'single' ? 'bg-[#2196f3] text-white' : 'bg-gray-100 text-gray-700');
            document.getElementById('tab-btn-excel').className = 'px-6 py-2.5 rounded-md font-semibold text-sm ' + (tab === 'excel' ? 'bg-[#2196f3] text-white' : 'bg-gray-100 text-gray-700');
        }
    </script>
</x-app-layout>
