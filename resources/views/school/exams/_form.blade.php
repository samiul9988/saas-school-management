@php($exam = $exam ?? null)

<div>
    <x-input-label for="name" value="Exam Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $exam?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="academic_year_id" value="Academic Year" />
    <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">—</option>
        @foreach ($academicYears as $year)
            <option value="{{ $year->id }}" @selected(old('academic_year_id', $exam?->academic_year_id) == $year->id)>{{ $year->name }}</option>
        @endforeach
    </select>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="starts_on" value="Starts On" />
        <x-text-input id="starts_on" type="date" name="starts_on" class="mt-1 block w-full" value="{{ old('starts_on', $exam?->starts_on?->format('Y-m-d')) }}" />
    </div>
    <div>
        <x-input-label for="ends_on" value="Ends On" />
        <x-text-input id="ends_on" type="date" name="ends_on" class="mt-1 block w-full" value="{{ old('ends_on', $exam?->ends_on?->format('Y-m-d')) }}" />
    </div>
</div>
<div>
    <x-input-label for="status" value="Status" />
    <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        @foreach (['upcoming', 'ongoing', 'completed'] as $status)
            <option value="{{ $status }}" @selected(old('status', $exam?->status ?? 'upcoming') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</div>
