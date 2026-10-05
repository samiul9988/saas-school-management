<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryItemController extends Controller
{
    public function index(): View
    {
        $items = InventoryItem::with('category')->orderBy('name')->get();

        return view('school.inventory.items.index', compact('items'));
    }

    public function create(): View
    {
        $categories = InventoryCategory::orderBy('name')->get();

        return view('school.inventory.items.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        InventoryItem::create($this->validated($request));

        return redirect()->route('school.inventory-items.index')->with('status', 'Item added.');
    }

    public function edit(InventoryItem $inventoryItem): View
    {
        $categories = InventoryCategory::orderBy('name')->get();

        return view('school.inventory.items.edit', ['item' => $inventoryItem, 'categories' => $categories]);
    }

    public function update(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $inventoryItem->update($this->validated($request));

        return redirect()->route('school.inventory-items.index')->with('status', 'Item updated.');
    }

    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        $inventoryItem->delete();

        return redirect()->route('school.inventory-items.index')->with('status', 'Item removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'inventory_category_id' => ['nullable', 'exists:inventory_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
        ]);
    }
}
