@php($teacher = $teacher ?? null)

<div>
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $teacher?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="email" value="Email" />
    <x-text-input id="email" type="email" name="email" class="mt-1 block w-full" value="{{ old('email', $teacher?->email) }}" />
</div>
<div>
    <x-input-label for="phone" value="Phone" />
    <x-text-input id="phone" name="phone" class="mt-1 block w-full" value="{{ old('phone', $teacher?->phone) }}" />
</div>
<div>
    <x-input-label for="designation" value="Designation" />
    <x-text-input id="designation" name="designation" class="mt-1 block w-full" value="{{ old('designation', $teacher?->designation) }}" />
</div>
<div>
    <x-input-label for="joining_date" value="Joining Date" />
    <x-text-input id="joining_date" type="date" name="joining_date" class="mt-1 block w-full" value="{{ old('joining_date', $teacher?->joining_date?->format('Y-m-d')) }}" />
</div>
<div>
    <x-input-label for="status" value="Status" />
    <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="active" @selected(old('status', $teacher?->status ?? 'active') === 'active')>Active</option>
        <option value="inactive" @selected(old('status', $teacher?->status) === 'inactive')>Inactive</option>
    </select>
</div>
