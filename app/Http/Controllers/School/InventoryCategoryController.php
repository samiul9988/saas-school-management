<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryCategoryController extends Controller
{
    public function index(): View
    {
        $categories = InventoryCategory::withCount('items')->orderBy('name')->get();

        return view('school.inventory.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => ['required', 'string', 'max:255']]);

        InventoryCategory::create($request->only('name'));

        return redirect()->route('school.inventory-categories.index')->with('status', 'Category added.');
    }

    public function destroy(InventoryCategory $inventoryCategory): RedirectResponse
    {
        $inventoryCategory->delete();

        return redirect()->route('school.inventory-categories.index')->with('status', 'Category removed.');
    }
}
