@php($route = $route ?? null)

<div>
    <x-input-label for="name" value="Route Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $route?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="vehicle_id" value="Vehicle" />
    <select id="vehicle_id" name="vehicle_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">—</option>
        @foreach ($vehicles as $vehicle)
            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $route?->vehicle_id) == $vehicle->id)>{{ $vehicle->name }}</option>
        @endforeach
    </select>
</div>
