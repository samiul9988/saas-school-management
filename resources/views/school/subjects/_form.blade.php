@php($subject = $subject ?? null)

<div>
    <x-input-label for="name" value="Subject Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $subject?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="code" value="Code" />
    <x-text-input id="code" name="code" class="mt-1 block w-full" value="{{ old('code', $subject?->code) }}" />
</div>
