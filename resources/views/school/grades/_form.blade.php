@php($grade = $grade ?? null)

<div>
    <x-input-label for="name" value="Grade Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $grade?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div class="grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="min_percent" value="Min %" />
        <x-text-input id="min_percent" type="number" step="0.01" name="min_percent" class="mt-1 block w-full" value="{{ old('min_percent', $grade?->min_percent) }}" required />
    </div>
    <div>
        <x-input-label for="max_percent" value="Max %" />
        <x-text-input id="max_percent" type="number" step="0.01" name="max_percent" class="mt-1 block w-full" value="{{ old('max_percent', $grade?->max_percent) }}" required />
    </div>
    <div>
        <x-input-label for="grade_point" value="Grade Point" />
        <x-text-input id="grade_point" type="number" step="0.01" name="grade_point" class="mt-1 block w-full" value="{{ old('grade_point', $grade?->grade_point ?? 0) }}" required />
    </div>
</div>
