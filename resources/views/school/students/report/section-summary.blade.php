<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Section Wise Student Summary') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="text-right text-sm text-gray-500">Total Student Found <span class="font-semibold">{{ $total }}</span></div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Class</th>
                        <th class="py-3 px-4">Section</th>
                        <th class="py-3 px-4">Total Student</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sections as $section)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $section->schoolClass?->name }}</td>
                            <td class="py-2 px-4">{{ $section->name }}</td>
                            <td class="py-2 px-4">{{ $section->students_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-6 text-center text-gray-400">No sections found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
