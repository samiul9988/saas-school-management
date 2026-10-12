<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('All Student List') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="text-right text-sm text-gray-500 mb-4">Total Student Found <span class="font-semibold">{{ $students->count() }}</span></div>

            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Select Column For Search" />
                    <select name="search_column" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select</option>
                        @foreach ($searchColumns as $key => $label)
                            <option value="{{ $key }}" @selected($searchColumn === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Search" />
                    <div class="flex gap-2">
                        <x-text-input name="search_value" class="mt-1 block w-full" value="{{ $searchValue }}" placeholder="Enter Search Value" />
                        <button type="submit" class="mt-1 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Class</th>
                        <th class="py-3 px-4">Group</th>
                        <th class="py-3 px-4">Roll</th>
                        <th class="py-3 px-4">Gender</th>
                        <th class="py-3 px-4">Religion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $student->admission_number }}</td>
                            <td class="py-2 px-4">{{ $student->name }}</td>
                            <td class="py-2 px-4">{{ $student->schoolClass?->name }}</td>
                            <td class="py-2 px-4">{{ $student->group_name }}</td>
                            <td class="py-2 px-4">{{ $student->roll }}</td>
                            <td class="py-2 px-4">{{ ucfirst((string) $student->gender) }}</td>
                            <td class="py-2 px-4">{{ $student->religion }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-gray-400">No students found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
