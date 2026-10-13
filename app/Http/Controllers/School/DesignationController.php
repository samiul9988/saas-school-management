<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesignationController extends Controller
{
    public function index(): View
    {
        $designations = Designation::orderBy('serial')->orderBy('id')->get();

        return view('school.designations.index', compact('designations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'integer', 'min:0'],
        ]);

        Designation::create([
            'name' => $data['name'],
            'serial' => $data['serial'] ?? 0,
        ]);

        return redirect()->route('school.designations.index')->with('status', 'Designation added.');
    }

    public function update(Request $request, Designation $designation): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable'],
        ]);

        $designation->update([
            'name' => $data['name'],
            'serial' => $data['serial'] ?? 0,
            'status' => $request->boolean('status') ? 'enable' : 'disable',
        ]);

        return redirect()->route('school.designations.index')->with('status', 'Designation updated.');
    }

    public function destroy(Designation $designation): RedirectResponse
    {
        $designation->delete();

        return redirect()->route('school.designations.index')->with('status', 'Designation deleted.');
    }
}
