@php($item = $item ?? null)

<div>
    <x-input-label for="name" value="Item Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $item?->name) }}" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="inventory_category_id" value="Category" />
    <select id="inventory_category_id" name="inventory_category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">—</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('inventory_category_id', $item?->inventory_category_id) == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
</div>
<div>
    <x-input-label for="unit" value="Unit" />
    <x-text-input id="unit" name="unit" class="mt-1 block w-full" value="{{ old('unit', $item?->unit) }}" placeholder="e.g. pcs, box, kg" />
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="quantity" value="Current Quantity" />
        <x-text-input id="quantity" type="number" name="quantity" class="mt-1 block w-full" value="{{ old('quantity', $item?->quantity ?? 0) }}" required />
    </div>
    <div>
        <x-input-label for="reorder_level" value="Reorder Level" />
        <x-text-input id="reorder_level" type="number" name="reorder_level" class="mt-1 block w-full" value="{{ old('reorder_level', $item?->reorder_level ?? 0) }}" required />
    </div>
</div>
