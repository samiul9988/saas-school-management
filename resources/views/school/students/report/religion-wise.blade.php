<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Religion Wise Student Summary') }} ({{ date('Y') }})</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="text-sm text-gray-500">Total Student Found <span class="font-semibold">{{ $total }}</span></div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Class</th>
                        @foreach ($religions as $religion)
                            <th class="py-3 px-4">{{ $religion }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $row['class'] }}</td>
                            @foreach ($religions as $religion)
                                <td class="py-2 px-4">{{ $row[$religion] ?? 0 }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ $religions->count() + 1 }}" class="py-6 text-center text-gray-400">No data found.</td></tr>
                    @endforelse
                    @if ($rows->isNotEmpty())
                        <tr class="border-t font-semibold bg-gray-50">
                            <td class="py-2 px-4">Total</td>
                            @foreach ($religions as $religion)
                                <td class="py-2 px-4">{{ $rows->sum($religion) }}</td>
                            @endforeach
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if ($rows->isNotEmpty())
            <div class="flex justify-end">
                <a href="{{ route('school.students.report.religion-wise.pdf') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#2196f3] text-white rounded-md font-semibold text-sm hover:bg-blue-600">
                    <i class="bi bi-download"></i> Download
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
