@php($student = $student ?? null)

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="academic_year_id" value="Academic Year" />
        <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">— Select Academic Year —</option>
            @foreach ($academicYears as $year)
                <option value="{{ $year->id }}" @selected(old('academic_year_id', $student?->academic_year_id) == $year->id)>{{ $year->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="school_class_id" value="Class" />
        <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">— Select Class —</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected(old('school_class_id', $student?->school_class_id) == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="grid grid-cols-3 gap-4">
    <div>
        <x-input-label for="section_id" value="Section" />
        <select id="section_id" name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">— Select Section —</option>
            @foreach ($classes as $class)
                @foreach ($class->sections as $section)
                    <option value="{{ $section->id }}" @selected(old('section_id', $student?->section_id) == $section->id)>{{ $class->name }} - {{ $section->name }}</option>
                @endforeach
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="group_name" value="Group" />
        <select id="group_name" name="group_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">— Select Group —</option>
            @foreach ($groups as $group)
                <option value="{{ $group }}" @selected(old('group_name', $student?->group_name) === $group)>{{ $group }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="category" value="Category" />
        <select id="category" name="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">— Select Category —</option>
            @foreach (['Regular', 'Freedom Fighter Quota', 'Disabled Quota', 'Other'] as $category)
                <option value="{{ $category }}" @selected(old('category', $student?->category) === $category)>{{ $category }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $student?->name) }}" placeholder="Enter Student Name" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="roll" value="Roll" />
        <x-text-input id="roll" name="roll" class="mt-1 block w-full" value="{{ old('roll', $student?->roll) }}" placeholder="Enter Roll" />
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="father_name" value="Father's Name" />
        <x-text-input id="father_name" name="father_name" class="mt-1 block w-full" value="{{ old('father_name', $student?->father_name) }}" placeholder="Enter Name" />
    </div>
    <div>
        <x-input-label for="mother_name" value="Mother's Name" />
        <x-text-input id="mother_name" name="mother_name" class="mt-1 block w-full" value="{{ old('mother_name', $student?->mother_name) }}" placeholder="Enter Name" />
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="admission_number" value="Admission Number" />
        <x-text-input id="admission_number" name="admission_number" class="mt-1 block w-full" value="{{ old('admission_number', $student?->admission_number) }}" />
    </div>
    <div>
        <x-input-label for="date_of_birth" value="Date of Birth" />
        <x-text-input id="date_of_birth" type="date" name="date_of_birth" class="mt-1 block w-full" value="{{ old('date_of_birth', $student?->date_of_birth?->format('Y-m-d')) }}" />
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="gender" value="Gender" />
        <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">— Select Gender —</option>
            <option value="male" @selected(old('gender', $student?->gender) === 'male')>Male</option>
            <option value="female" @selected(old('gender', $student?->gender) === 'female')>Female</option>
            <option value="other" @selected(old('gender', $student?->gender) === 'other')>Other</option>
        </select>
    </div>
    <div>
        <x-input-label for="religion" value="Religion" />
        <select id="religion" name="religion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">— Select Religion —</option>
            @foreach (['Islam', 'Hinduism', 'Christianity', 'Buddhism', 'Other'] as $religion)
                <option value="{{ $religion }}" @selected(old('religion', $student?->religion) === $religion)>{{ $religion }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="guardian_phone" value="Mobile" />
        <x-text-input id="guardian_phone" name="guardian_phone" class="mt-1 block w-full" value="{{ old('guardian_phone', $student?->guardian_phone) }}" placeholder="Enter Phone" />
    </div>
    <div>
        <x-input-label for="guardian_name" value="Guardian Name" />
        <x-text-input id="guardian_name" name="guardian_name" class="mt-1 block w-full" value="{{ old('guardian_name', $student?->guardian_name) }}" />
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="blood_group" value="Blood Group" />
        <select id="blood_group" name="blood_group" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">— Select Blood Group —</option>
            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bloodGroup)
                <option value="{{ $bloodGroup }}" @selected(old('blood_group', $student?->blood_group) === $bloodGroup)>{{ $bloodGroup }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="village" value="Village" />
        <x-text-input id="village" name="village" class="mt-1 block w-full" value="{{ old('village', $student?->village) }}" />
    </div>
    <div>
        <x-input-label for="post_office" value="Post Office" />
        <x-text-input id="post_office" name="post_office" class="mt-1 block w-full" value="{{ old('post_office', $student?->post_office) }}" />
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="upazila_thana" value="Upazila / Thana" />
        <x-text-input id="upazila_thana" name="upazila_thana" class="mt-1 block w-full" value="{{ old('upazila_thana', $student?->upazila_thana) }}" />
    </div>
    <div>
        <x-input-label for="district" value="District" />
        <x-text-input id="district" name="district" class="mt-1 block w-full" value="{{ old('district', $student?->district) }}" />
    </div>
</div>
<div>
    <x-input-label for="status" value="Status" />
    <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        @foreach (['active', 'inactive', 'transferred', 'graduated'] as $status)
            <option value="{{ $status }}" @selected(old('status', $student?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</div>
