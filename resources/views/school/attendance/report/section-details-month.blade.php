<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Section Wise Monthly Details') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-64">
                    <select name="section_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                {{ $sectionOption->schoolClass?->name }} -{{ $sectionOption->shift?->name ?? 'N/A' }}-{{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="period_id" class="block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Period</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected((string) request('period_id') === (string) $period->id)>{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="year" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected((string) request('year', now()->year) === (string) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="month" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" @selected((string) request('month', now()->month) === (string) $m)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>

        @if ($section)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-2 px-3 sticky left-0 bg-blue-100">ID</th>
                            <th class="py-2 px-3 sticky left-0 bg-blue-100">Name</th>
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                <th class="py-2 px-2 text-center">{{ $d }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-t">
                                <td class="py-2 px-3">{{ $row['id'] }}</td>
                                <td class="py-2 px-3">{{ $row['name'] }}</td>
                                @for ($d = 1; $d <= $daysInMonth; $d++)
                                    @php($record = $row['byDay']->get($d))
                                    <td class="py-2 px-2 text-center">
                                        @if ($record)
                                            <span class="{{ $record->status === 'present' ? 'text-green-600' : ($record->status === 'absent' ? 'text-red-600' : 'text-amber-600') }}">
                                                {{ strtoupper(substr($record->status, 0, 1)) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @empty
                            <tr><td colspan="{{ $daysInMonth + 2 }}" class="py-6 text-center text-gray-400">No Data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
