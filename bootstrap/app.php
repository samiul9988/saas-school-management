<?php

use App\Http\Middleware\AllowActivationAccess;
use App\Http\Middleware\EnsureModulePermission;
use App\Http\Middleware\EnsureParent;
use App\Http\Middleware\EnsureSchoolContext;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\MaintenanceMode;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'super_admin' => EnsureSuperAdmin::class,
            'school_context' => EnsureSchoolContext::class,
            'module_permission' => EnsureModulePermission::class,
            'parent_portal' => EnsureParent::class,
            'activation_access' => AllowActivationAccess::class,
        ]);
        $middleware->web(append: [
            MaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
