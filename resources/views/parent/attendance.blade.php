<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Attendance') }} &mdash; {{ $student->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50"><tr class="text-gray-500"><th class="py-3 px-4">Date</th><th class="py-3 px-4">Status</th></tr></thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $attendance->date->format('Y-m-d') }}</td>
                                <td class="py-3 px-4 capitalize">{{ $attendance->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $attendances->links() }}
        </div>
    </div>
</x-app-layout>
