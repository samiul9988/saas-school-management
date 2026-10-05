@php($schoolClass = $schoolClass ?? null)

<div>
    <x-input-label for="name" value="Class Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $schoolClass?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="sort_order" value="Sort Order" />
    <x-text-input id="sort_order" type="number" name="sort_order" class="mt-1 block w-full" value="{{ old('sort_order', $schoolClass?->sort_order ?? 0) }}" />
</div>
