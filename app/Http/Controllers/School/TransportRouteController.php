<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransportRouteController extends Controller
{
    public function index(): View
    {
        $routes = TransportRoute::with('vehicle', 'stops')->withCount('studentTransports')->orderBy('name')->get();

        return view('school.transport.routes.index', compact('routes'));
    }

    public function create(): View
    {
        $vehicles = Vehicle::orderBy('name')->get();

        return view('school.transport.routes.create', compact('vehicles'));
    }

    public function store(Request $request): RedirectResponse
    {
        TransportRoute::create($this->validated($request));

        return redirect()->route('school.transport-routes.index')->with('status', 'Route added.');
    }

    public function edit(TransportRoute $transportRoute): View
    {
        $vehicles = Vehicle::orderBy('name')->get();

        return view('school.transport.routes.edit', ['route' => $transportRoute, 'vehicles' => $vehicles]);
    }

    public function update(Request $request, TransportRoute $transportRoute): RedirectResponse
    {
        $transportRoute->update($this->validated($request));

        return redirect()->route('school.transport-routes.index')->with('status', 'Route updated.');
    }

    public function destroy(TransportRoute $transportRoute): RedirectResponse
    {
        $transportRoute->delete();

        return redirect()->route('school.transport-routes.index')->with('status', 'Route removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
        ]);
    }
}
