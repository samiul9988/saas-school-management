<?php

namespace App\Services;

use App\Models\School;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(School $school, string $to, string $message, ?string $context = null): SmsLog
    {
        $log = SmsLog::create([
            'school_id' => $school->id,
            'to' => $to,
            'message' => $message,
            'context' => $context,
            'status' => 'queued',
        ]);

        if (! $school->sms_api_key) {
            Log::info("SMS (no provider configured) to {$to}: {$message}");

            return $log;
        }

        try {
            Http::timeout(5)->post('https://api.example-sms-gateway.test/send', [
                'api_key' => $school->sms_api_key,
                'sender_id' => $school->sms_sender_id,
                'to' => $to,
                'message' => $message,
            ]);

            $log->update(['status' => 'sent']);
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed']);
            Log::warning("SMS send failed to {$to}: {$e->getMessage()}");
        }

        return $log;
    }
}
