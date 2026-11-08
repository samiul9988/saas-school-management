<?php

namespace App\Http\Middleware;

use App\Support\ModulePermissionMap;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModulePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $module = ModulePermissionMap::moduleForRouteName($request->route()?->getName());

        if ($user && $module) {
            $action = ModulePermissionMap::actionForMethod($request->method());

            if (! $user->hasModuleAccess($module, $action)) {
                abort(403, 'You do not have permission to access this section.');
            }
        }

        return $next($request);
    }
}
