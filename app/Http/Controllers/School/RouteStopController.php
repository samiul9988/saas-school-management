<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\RouteStop;
use App\Models\TransportRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RouteStopController extends Controller
{
    public function store(Request $request, TransportRoute $route): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'fare' => ['required', 'numeric', 'min:0'],
        ]);

        $data['transport_route_id'] = $route->id;

        RouteStop::create($data);

        return redirect()->route('school.transport-routes.index')->with('status', 'Stop added.');
    }

    public function destroy(TransportRoute $route, RouteStop $stop): RedirectResponse
    {
        $stop->delete();

        return redirect()->route('school.transport-routes.index')->with('status', 'Stop removed.');
    }
}
