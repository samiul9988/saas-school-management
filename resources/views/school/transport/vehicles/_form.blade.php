@php($vehicle = $vehicle ?? null)

<div>
    <x-input-label for="name" value="Vehicle Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $vehicle?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="number_plate" value="Number Plate" />
    <x-text-input id="number_plate" name="number_plate" class="mt-1 block w-full" value="{{ old('number_plate', $vehicle?->number_plate) }}" />
</div>
<div>
    <x-input-label for="capacity" value="Capacity" />
    <x-text-input id="capacity" type="number" name="capacity" class="mt-1 block w-full" value="{{ old('capacity', $vehicle?->capacity) }}" />
</div>
<div>
    <x-input-label for="driver_name" value="Driver Name" />
    <x-text-input id="driver_name" name="driver_name" class="mt-1 block w-full" value="{{ old('driver_name', $vehicle?->driver_name) }}" />
</div>
<div>
    <x-input-label for="driver_phone" value="Driver Phone" />
    <x-text-input id="driver_phone" name="driver_phone" class="mt-1 block w-full" value="{{ old('driver_phone', $vehicle?->driver_phone) }}" />
</div>
