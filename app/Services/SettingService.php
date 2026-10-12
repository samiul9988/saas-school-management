<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function __construct(protected Setting $setting)
    {
    }

    public function modify(string $key, string $value): bool
    {
        return $this->setting->updateOrCreate(['key' => $key], ['value' => $value]) !== null;
    }
}
