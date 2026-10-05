<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Transport Routes') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.transport-routes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Route
                </a>
            </div>

            @foreach ($routes as $route)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="font-semibold">{{ $route->name }}</div>
                            <div class="text-sm text-gray-500">
                                Vehicle: {{ $route->vehicle?->name ?? '—' }} &middot;
                                Students: {{ $route->student_transports_count }}
                            </div>
                        </div>
                        <div class="space-x-2 text-sm">
                            <a href="{{ route('school.transport-routes.edit', $route) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('school.transport-routes.destroy', $route) }}" method="POST" class="inline" onsubmit="return confirm('Delete this route?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>

                    <table class="w-full text-sm text-left mb-3">
                        <thead><tr class="text-gray-500 border-b"><th class="py-2">Stop</th><th class="py-2">Fare</th><th class="py-2"></th></tr></thead>
                        <tbody>
                            @foreach ($route->stops as $stop)
                                <tr class="border-b">
                                    <td class="py-2">{{ $stop->name }}</td>
                                    <td class="py-2">{{ number_format($stop->fare, 2) }}</td>
                                    <td class="py-2">
                                        <form action="{{ route('school.transport-routes.stops.destroy', [$route, $stop]) }}" method="POST" onsubmit="return confirm('Remove this stop?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-xs">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <form action="{{ route('school.transport-routes.stops.store', $route) }}" method="POST" class="flex gap-2">
                        @csrf
                        <x-text-input name="name" class="flex-1" placeholder="Stop name" required />
                        <x-text-input name="fare" type="number" step="0.01" class="w-28" placeholder="Fare" required />
                        <x-primary-button>Add Stop</x-primary-button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
