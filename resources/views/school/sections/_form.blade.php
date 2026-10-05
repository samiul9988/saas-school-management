@php($section = $section ?? null)

<div>
    <x-input-label for="school_class_id" value="Class" />
    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        <option value="">— Select class —</option>
        @foreach ($classes as $class)
            <option value="{{ $class->id }}" @selected(old('school_class_id', $section?->school_class_id) == $class->id)>{{ $class->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('school_class_id')" class="mt-2" />
</div>
<div>
    <x-input-label for="name" value="Section Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $section?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="capacity" value="Capacity" />
    <x-text-input id="capacity" type="number" name="capacity" class="mt-1 block w-full" value="{{ old('capacity', $section?->capacity) }}" />
</div>
