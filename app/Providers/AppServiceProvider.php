<?php

namespace App\Providers;

use App\Models\SmsGatewaySetting;
use App\Services\Sms\BulkSmsBdGateway;
use App\Services\Sms\SmsGatewayAdapterInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsGatewayAdapterInterface::class, function () {
            return new BulkSmsBdGateway(SmsGatewaySetting::current());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
