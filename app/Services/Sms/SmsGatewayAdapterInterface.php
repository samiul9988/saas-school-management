<?php

namespace App\Services\Sms;

interface SmsGatewayAdapterInterface
{
    public function send(string $to, string $message): SmsGatewayResult;

    /**
     * Returns the provider's real balance, or null if the provider does not support balance lookup.
     */
    public function getBalance(): ?int;
}
