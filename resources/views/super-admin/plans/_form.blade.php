@php($plan = $plan ?? null)

<div>
    <x-input-label for="name" value="Plan Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $plan?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="description" value="Description" />
    <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $plan?->description) }}</textarea>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="monthly_price" value="Monthly Price" />
        <x-text-input id="monthly_price" type="number" step="0.01" name="monthly_price" class="mt-1 block w-full" value="{{ old('monthly_price', $plan?->monthly_price ?? 0) }}" required />
    </div>
    <div>
        <x-input-label for="yearly_price" value="Yearly Price" />
        <x-text-input id="yearly_price" type="number" step="0.01" name="yearly_price" class="mt-1 block w-full" value="{{ old('yearly_price', $plan?->yearly_price ?? 0) }}" required />
    </div>
</div>
<div class="grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="max_students" value="Max Students" />
        <x-text-input id="max_students" type="number" name="max_students" class="mt-1 block w-full" value="{{ old('max_students', $plan?->max_students) }}" />
    </div>
    <div>
        <x-input-label for="max_teachers" value="Max Teachers" />
        <x-text-input id="max_teachers" type="number" name="max_teachers" class="mt-1 block w-full" value="{{ old('max_teachers', $plan?->max_teachers) }}" />
    </div>
    <div>
        <x-input-label for="max_branches" value="Max Branches" />
        <x-text-input id="max_branches" type="number" name="max_branches" class="mt-1 block w-full" value="{{ old('max_branches', $plan?->max_branches ?? 1) }}" required />
    </div>
</div>
<div>
    <x-input-label for="features" value="Features (comma separated)" />
    <x-text-input id="features" name="features" class="mt-1 block w-full" value="{{ old('features', $plan ? implode(', ', $plan->features ?? []) : '') }}" placeholder="students, teachers, attendance, exams" />
</div>
<div>
    <x-input-label for="sort_order" value="Sort Order" />
    <x-text-input id="sort_order" type="number" name="sort_order" class="mt-1 block w-full" value="{{ old('sort_order', $plan?->sort_order ?? 0) }}" />
</div>
<div class="flex items-center gap-2">
    <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $plan?->is_active ?? true))>
    <x-input-label for="is_active" value="Active" />
</div>
