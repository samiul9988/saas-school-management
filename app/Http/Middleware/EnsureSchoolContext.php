<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->school_id) {
            abort(403, 'Unauthorized.');
        }

        if (! $user->school || ! $user->school->isActive()) {
            abort(403, 'Your school account is not active. Please contact support.');
        }

        return $next($request);
    }
}
