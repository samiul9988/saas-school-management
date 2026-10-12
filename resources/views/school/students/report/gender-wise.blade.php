<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Gender Wise Student Summary') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <x-input-label value="Year" />
                    <select name="year" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->name }}" @selected($year === $academicYear->name)>{{ $academicYear->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Search By" />
                    <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="class">Class</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>

        @if ($year)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Total Student</th>
                            <th class="py-3 px-4">Male</th>
                            <th class="py-3 px-4">Female</th>
                            <th class="py-3 px-4">Other</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $row['class'] }}</td>
                                <td class="py-2 px-4">{{ $row['total'] }}</td>
                                <td class="py-2 px-4">{{ $row['male'] }}</td>
                                <td class="py-2 px-4">{{ $row['female'] }}</td>
                                <td class="py-2 px-4">{{ $row['other'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-gray-400">No data found.</td></tr>
                        @endforelse
                        @if ($rows->isNotEmpty())
                            <tr class="border-t font-semibold bg-gray-50">
                                <td class="py-2 px-4">Total</td>
                                <td class="py-2 px-4">{{ $rows->sum('total') }}</td>
                                <td class="py-2 px-4">{{ $rows->sum('male') }}</td>
                                <td class="py-2 px-4">{{ $rows->sum('female') }}</td>
                                <td class="py-2 px-4">{{ $rows->sum('other') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($rows->isNotEmpty())
                <div class="flex justify-end">
                    <a href="{{ route('school.students.report.gender-wise.pdf', ['year' => $year]) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#2196f3] text-white rounded-md font-semibold text-sm hover:bg-blue-600">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
