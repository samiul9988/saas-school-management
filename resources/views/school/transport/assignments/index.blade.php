<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Transport Assignments') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.student-transports.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Assign Student
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Student</th>
                            <th class="py-3 px-4">Route</th>
                            <th class="py-3 px-4">Stop</th>
                            <th class="py-3 px-4">Monthly Fee</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignments as $assignment)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $assignment->student->name }}</td>
                                <td class="py-3 px-4">{{ $assignment->route->name }}</td>
                                <td class="py-3 px-4">{{ $assignment->stop?->name ?? '—' }}</td>
                                <td class="py-3 px-4">{{ number_format($assignment->monthly_fee, 2) }}</td>
                                <td class="py-3 px-4">
                                    <form action="{{ route('school.student-transports.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Remove this assignment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $assignments->links() }}
        </div>
    </div>
</x-app-layout>
