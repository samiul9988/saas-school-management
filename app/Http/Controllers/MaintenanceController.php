<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function __construct(protected SettingService $settingService)
    {
    }

    public function show(Request $request): View
    {
        return view('dashboard.activation', [
            'active' => config('settings.maintenance') === 'y',
            'key' => $request->query('key'),
        ]);
    }

    public function toggle(Request $request): RedirectResponse
    {
        $turningOn = config('settings.maintenance') !== 'y';

        $this->settingService->modify('maintenance', $turningOn ? 'y' : 'n');
        Cache::forget('settings');

        $status = $turningOn
            ? 'Maintenance mode is now on — visitors will see the maintenance page.'
            : 'Maintenance mode is now off — the site is live for everyone.';

        return redirect()->route('maintenance.show', $request->filled('key') ? ['key' => $request->input('key')] : [])
            ->with('status', $status);
    }

    public function preview(): View
    {
        return view('errors.503');
    }
}
