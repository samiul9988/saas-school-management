@php($feeType = $feeType ?? null)

<div>
    <x-input-label for="name" value="Fee Type Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $feeType?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="default_amount" value="Default Amount" />
    <x-text-input id="default_amount" type="number" step="0.01" name="default_amount" class="mt-1 block w-full" value="{{ old('default_amount', $feeType?->default_amount ?? 0) }}" required />
</div>
<div>
    <x-input-label for="frequency" value="Frequency" />
    <select id="frequency" name="frequency" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="one_time" @selected(old('frequency', $feeType?->frequency ?? 'one_time') === 'one_time')>One Time</option>
        <option value="monthly" @selected(old('frequency', $feeType?->frequency) === 'monthly')>Monthly</option>
        <option value="yearly" @selected(old('frequency', $feeType?->frequency) === 'yearly')>Yearly</option>
    </select>
</div>
