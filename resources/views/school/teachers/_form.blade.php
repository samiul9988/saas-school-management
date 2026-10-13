@php($teacher = $teacher ?? null)

<div>
    <x-input-label for="name" value="Teacher / Staff Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $teacher?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="father_name" value="Father's Name" />
        <x-text-input id="father_name" name="father_name" class="mt-1 block w-full" value="{{ old('father_name', $teacher?->father_name) }}" />
    </div>
    <div>
        <x-input-label for="mother_name" value="Mother's Name" />
        <x-text-input id="mother_name" name="mother_name" class="mt-1 block w-full" value="{{ old('mother_name', $teacher?->mother_name) }}" />
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="phone" value="Mobile Number" />
        <x-text-input id="phone" name="phone" class="mt-1 block w-full" value="{{ old('phone', $teacher?->phone) }}" />
    </div>
    <div>
        <x-input-label for="gender" value="Gender" />
        <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Select Gender</option>
            <option value="male" @selected(old('gender', $teacher?->gender) === 'male')>Male</option>
            <option value="female" @selected(old('gender', $teacher?->gender) === 'female')>Female</option>
            <option value="other" @selected(old('gender', $teacher?->gender) === 'other')>Other</option>
        </select>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="religion" value="Religion" />
        <select id="religion" name="religion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Select Religion</option>
            @foreach (['Islam', 'Hinduism', 'Christianity', 'Buddhism', 'Other'] as $religion)
                <option value="{{ $religion }}" @selected(old('religion', $teacher?->religion) === $religion)>{{ $religion }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="blood_group" value="Blood Group" />
        <select id="blood_group" name="blood_group" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Select Blood Group</option>
            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                <option value="{{ $group }}" @selected(old('blood_group', $teacher?->blood_group) === $group)>{{ $group }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <x-input-label for="address" value="Address" />
    <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('address', $teacher?->address) }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" type="email" name="email" class="mt-1 block w-full" value="{{ old('email', $teacher?->email) }}" />
    </div>
    <div>
        <x-input-label for="designation" value="Designation" />
        <x-text-input id="designation" name="designation" class="mt-1 block w-full" value="{{ old('designation', $teacher?->designation) }}" />
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
</div>
