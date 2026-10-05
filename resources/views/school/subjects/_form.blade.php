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
<div>
    <x-input-label for="school_class_id" value="Class (optional)" />
    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">— All classes —</option>
        @foreach ($classes as $class)
            <option value="{{ $class->id }}" @selected(old('school_class_id', $subject?->school_class_id) == $class->id)>{{ $class->name }}</option>
        @endforeach
    </select>
</div>
