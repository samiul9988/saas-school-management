<?php

namespace App\Services\Sms;

class SmsGatewayResult
{
    public function __construct(
        public readonly bool $success,
        public readonly bool $unknown,
        public readonly ?string $providerReference = null,
        public readonly ?string $error = null,
    ) {}

    public static function success(?string $providerReference = null): self
    {
        return new self(success: true, unknown: false, providerReference: $providerReference);
    }

    public static function failed(?string $error = null): self
    {
        return new self(success: false, unknown: false, error: $error);
    }

    public static function unknown(?string $error = null): self
    {
        return new self(success: false, unknown: true, error: $error);
    }
}
