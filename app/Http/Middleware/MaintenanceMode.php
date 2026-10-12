<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('dashboard/activation*')) {
            return $next($request);
        }

        if (config('settings.maintenance') === 'y') {
            abort(503);
        }

        return $next($request);
    }
}
