@php($academicYear = $academicYear ?? null)

<div>
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $academicYear?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="starts_on" value="Starts On" />
        <x-text-input id="starts_on" type="date" name="starts_on" class="mt-1 block w-full" value="{{ old('starts_on', $academicYear?->starts_on?->format('Y-m-d')) }}" required />
    </div>
    <div>
        <x-input-label for="ends_on" value="Ends On" />
        <x-text-input id="ends_on" type="date" name="ends_on" class="mt-1 block w-full" value="{{ old('ends_on', $academicYear?->ends_on?->format('Y-m-d')) }}" required />
    </div>
</div>
<div class="flex items-center gap-2">
    <input type="checkbox" id="is_current" name="is_current" value="1" @checked(old('is_current', $academicYear?->is_current))>
    <x-input-label for="is_current" value="Set as current academic year" />
</div>
