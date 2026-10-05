@php($student = $student ?? null)

<div>
    <x-input-label for="name" value="Student Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $student?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="admission_number" value="Admission Number" />
        <x-text-input id="admission_number" name="admission_number" class="mt-1 block w-full" value="{{ old('admission_number', $student?->admission_number) }}" />
    </div>
    <div>
        <x-input-label for="roll" value="Roll" />
        <x-text-input id="roll" name="roll" class="mt-1 block w-full" value="{{ old('roll', $student?->roll) }}" />
    </div>
</div>
<div class="grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="academic_year_id" value="Academic Year" />
        <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">—</option>
            @foreach ($academicYears as $year)
                <option value="{{ $year->id }}" @selected(old('academic_year_id', $student?->academic_year_id) == $year->id)>{{ $year->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="school_class_id" value="Class" />
        <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">—</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected(old('school_class_id', $student?->school_class_id) == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="section_id" value="Section" />
        <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">—</option>
            @foreach ($classes as $class)
                @foreach ($class->sections as $section)
                    <option value="{{ $section->id }}" @selected(old('section_id', $student?->section_id) == $section->id)>{{ $class->name }} - {{ $section->name }}</option>
                @endforeach
            @endforeach
        </select>
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="date_of_birth" value="Date of Birth" />
        <x-text-input id="date_of_birth" type="date" name="date_of_birth" class="mt-1 block w-full" value="{{ old('date_of_birth', $student?->date_of_birth?->format('Y-m-d')) }}" />
    </div>
    <div>
        <x-input-label for="gender" value="Gender" />
        <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">—</option>
            <option value="male" @selected(old('gender', $student?->gender) === 'male')>Male</option>
            <option value="female" @selected(old('gender', $student?->gender) === 'female')>Female</option>
            <option value="other" @selected(old('gender', $student?->gender) === 'other')>Other</option>
        </select>
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="guardian_name" value="Guardian Name" />
        <x-text-input id="guardian_name" name="guardian_name" class="mt-1 block w-full" value="{{ old('guardian_name', $student?->guardian_name) }}" />
    </div>
    <div>
        <x-input-label for="guardian_phone" value="Guardian Phone" />
        <x-text-input id="guardian_phone" name="guardian_phone" class="mt-1 block w-full" value="{{ old('guardian_phone', $student?->guardian_phone) }}" />
    </div>
</div>
<div>
    <x-input-label for="address" value="Address" />
    <x-text-input id="address" name="address" class="mt-1 block w-full" value="{{ old('address', $student?->address) }}" />
</div>
<div>
    <x-input-label for="status" value="Status" />
    <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        @foreach (['active', 'inactive', 'transferred', 'graduated'] as $status)
            <option value="{{ $status }}" @selected(old('status', $student?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</div>
