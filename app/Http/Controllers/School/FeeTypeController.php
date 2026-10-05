<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeTypeController extends Controller
{
    public function index(): View
    {
        $feeTypes = FeeType::orderBy('name')->get();

        return view('school.fee-types.index', compact('feeTypes'));
    }

    public function create(): View
    {
        return view('school.fee-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        FeeType::create($this->validated($request));

        return redirect()->route('school.fee-types.index')->with('status', 'Fee type created.');
    }

    public function edit(FeeType $feeType): View
    {
        return view('school.fee-types.edit', compact('feeType'));
    }

    public function update(Request $request, FeeType $feeType): RedirectResponse
    {
        $feeType->update($this->validated($request));

        return redirect()->route('school.fee-types.index')->with('status', 'Fee type updated.');
    }

    public function destroy(FeeType $feeType): RedirectResponse
    {
        $feeType->delete();

        return redirect()->route('school.fee-types.index')->with('status', 'Fee type deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'default_amount' => ['required', 'numeric', 'min:0'],
            'frequency' => ['required', 'in:one_time,monthly,yearly'],
        ]);
    }
}
