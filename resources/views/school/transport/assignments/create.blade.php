<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Assign Transport') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.student-transports.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="student_id" value="Student" />
                        <select id="student_id" name="student_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">— Select student —</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="transport_route_id" value="Route" />
                        <select id="transport_route_id" name="transport_route_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required onchange="document.querySelectorAll('.stop-group').forEach(el => el.style.display = el.dataset.route === this.value ? '' : 'none')">
                            <option value="">— Select route —</option>
                            @foreach ($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('transport_route_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="route_stop_id" value="Stop" />
                        <select id="route_stop_id" name="route_stop_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">—</option>
                            @foreach ($routes as $route)
                                @foreach ($route->stops as $stop)
                                    <option value="{{ $stop->id }}" class="stop-option" data-route="{{ $route->id }}">{{ $route->name }} — {{ $stop->name }} ({{ number_format($stop->fare, 2) }})</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="monthly_fee" value="Monthly Fee" />
                        <x-text-input id="monthly_fee" type="number" step="0.01" name="monthly_fee" class="mt-1 block w-full" value="{{ old('monthly_fee', 0) }}" required />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Assign</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
