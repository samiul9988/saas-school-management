<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(): View
    {
        $vehicles = Vehicle::orderBy('name')->get();

        return view('school.transport.vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        return view('school.transport.vehicles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Vehicle::create($this->validated($request));

        return redirect()->route('school.vehicles.index')->with('status', 'Vehicle added.');
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('school.transport.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($this->validated($request));

        return redirect()->route('school.vehicles.index')->with('status', 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('school.vehicles.index')->with('status', 'Vehicle removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number_plate' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:30'],
        ]);
    }
}
