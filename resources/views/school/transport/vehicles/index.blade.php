<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Vehicles') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-between">
                <div class="space-x-4 text-sm">
                    <a href="{{ route('school.transport-routes.index') }}" class="text-indigo-600 hover:underline">Routes</a>
                    <a href="{{ route('school.student-transports.index') }}" class="text-indigo-600 hover:underline">Student Assignments</a>
                </div>
                <a href="{{ route('school.vehicles.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Vehicle
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Number Plate</th>
                            <th class="py-3 px-4">Capacity</th>
                            <th class="py-3 px-4">Driver</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicles as $vehicle)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $vehicle->name }}</td>
                                <td class="py-3 px-4">{{ $vehicle->number_plate ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $vehicle->capacity ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $vehicle->driver_name ?? '—' }} {{ $vehicle->driver_phone ? '('.$vehicle->driver_phone.')' : '' }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.vehicles.edit', $vehicle) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('school.vehicles.destroy', $vehicle) }}" method="POST" class="inline" onsubmit="return confirm('Delete this vehicle?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
