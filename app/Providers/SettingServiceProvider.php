<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Factory $cache, Setting $settings): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $values = $cache->remember('settings', 60, function () use ($settings) {
                    return $settings->pluck('value', 'key')->all();
                });
                config()->set('settings', $values);
            }
        } catch (\Throwable $e) {
            // DB not reachable yet (e.g. during initial artisan commands) — ignore.
        }
    }
}
