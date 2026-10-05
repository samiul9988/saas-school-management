<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InventoryTransactionController extends Controller
{
    public function store(Request $request, InventoryItem $item): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['type'] === 'out' && $data['quantity'] > $item->quantity) {
            return back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        $data['inventory_item_id'] = $item->id;
        InventoryTransaction::create($data);

        $item->increment('quantity', $data['type'] === 'in' ? $data['quantity'] : -$data['quantity']);

        return redirect()->route('school.inventory-items.index')->with('status', 'Stock updated.');
    }
}
