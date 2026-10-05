<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isParent() || ! $user->portalStudent) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
