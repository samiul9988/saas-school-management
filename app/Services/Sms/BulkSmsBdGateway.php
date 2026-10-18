<?php

namespace App\Services\Sms;

use App\Models\SmsGatewaySetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Adapter for the BulkSMSBD gateway (https://bulksmsbd.net).
 *
 * NOTE: the exact endpoint names and parameters below (`api/smsapi`, `api/getBalanceApi`,
 * `api_key`/`senderid`/`number`/`message`) are reconstructed from BulkSMSBD's public
 * WordPress/Laravel integrations rather than official documentation. Confirm these against
 * the account dashboard before relying on this in production.
 */
class BulkSmsBdGateway implements SmsGatewayAdapterInterface
{
    public function __construct(private SmsGatewaySetting $settings) {}

    public function send(string $to, string $message): SmsGatewayResult
    {
        if (! $this->settings->api_key) {
            return SmsGatewayResult::failed('SMS gateway is not configured.');
        }

        try {
            $response = Http::timeout(10)->get($this->baseUrl().'/api/smsapi', [
                'api_key' => $this->settings->api_key,
                'senderid' => $this->settings->sender_id,
                'number' => $to,
                'message' => $message,
            ]);

            if (! $response->successful()) {
                return SmsGatewayResult::unknown('Gateway responded with HTTP '.$response->status());
            }

            $body = $response->json();
            $responseCode = $body['response_code'] ?? null;

            if ($responseCode === 202) {
                return SmsGatewayResult::success((string) ($body['message_id'] ?? null));
            }

            return SmsGatewayResult::failed($body['error_message'] ?? 'Gateway rejected the message.');
        } catch (ConnectionException $e) {
            Log::warning("BulkSMSBD send timeout/connection error to {$to}: {$e->getMessage()}");

            return SmsGatewayResult::unknown($e->getMessage());
        } catch (\Throwable $e) {
            Log::error("BulkSMSBD send failed to {$to}: {$e->getMessage()}");

            return SmsGatewayResult::unknown($e->getMessage());
        }
    }

    public function getBalance(): ?int
    {
        if (! $this->settings->api_key) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get($this->baseUrl().'/api/getBalanceApi', [
                'api_key' => $this->settings->api_key,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $balance = $response->json('balance');

            return $balance !== null ? (int) $balance : null;
        } catch (\Throwable $e) {
            Log::warning("BulkSMSBD balance lookup failed: {$e->getMessage()}");

            return null;
        }
    }

    private function baseUrl(): string
    {
        return rtrim($this->settings->api_base_url ?: 'http://bulksmsbd.net', '/');
    }
}
