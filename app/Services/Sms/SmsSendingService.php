<?php

namespace App\Services\Sms;

use App\Exceptions\Sms\InsufficientCentralCreditsException;
use App\Exceptions\Sms\InsufficientSchoolCreditsException;
use App\Models\School;
use App\Models\SmsGatewaySetting;
use App\Models\SmsSendRequest;
use Illuminate\Support\Facades\DB;

class SmsSendingService
{
    public function __construct(
        private SchoolSmsWalletService $wallets,
        private CentralSmsLedgerService $centralLedger,
        private SmsSegmentCalculator $segments,
    ) {}

    /**
     * @param  string[]  $recipients
     */
    public function send(School $school, array $recipients, string $message, string $idempotencyKey, ?string $context = null, ?int $createdBy = null): SmsSendRequest
    {
        $sendRequest = DB::transaction(function () use ($school, $recipients, $message, $idempotencyKey, $context, $createdBy) {
            $existing = SmsSendRequest::withoutGlobalScope('school')
                ->where('idempotency_key', $idempotencyKey)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $segments = $this->segments->calculate($message);
            $creditsRequired = $segments * count($recipients);

            $wallet = $this->wallets->lockWalletFor($school);

            if ($wallet->balance < $creditsRequired) {
                throw new InsufficientSchoolCreditsException;
            }

            $settings = SmsGatewaySetting::query()->lockForUpdate()->firstOrCreate([]);

            if ($settings->central_balance < $creditsRequired) {
                throw new InsufficientCentralCreditsException;
            }

            $sendRequest = SmsSendRequest::create([
                'school_id' => $school->id,
                'idempotency_key' => $idempotencyKey,
                'recipients' => $recipients,
                'message' => $message,
                'context' => $context,
                'segments' => $segments,
                'credits_required' => $creditsRequired,
                'status' => 'reserved',
                'created_by' => $createdBy,
            ]);

            $this->wallets->record($wallet, 'usage', -$creditsRequired, SmsSendRequest::class, $sendRequest->id, 'SMS send reservation', $createdBy);
            $this->centralLedger->record($settings, 'usage', -$creditsRequired, SmsSendRequest::class, $sendRequest->id, 'SMS send reservation', $createdBy);

            return $sendRequest;
        });

        if ($sendRequest->status !== 'reserved') {
            return $sendRequest;
        }

        return $this->dispatchToGateway($school, $sendRequest, $recipients, $message);
    }

    private function dispatchToGateway(School $school, SmsSendRequest $sendRequest, array $recipients, string $message): SmsSendRequest
    {
        $gateway = app(SmsGatewayAdapterInterface::class);

        $failures = [];
        $lastReference = null;

        foreach ($recipients as $to) {
            $result = $gateway->send($to, $message);

            if ($result->unknown) {
                $sendRequest->update(['status' => 'unknown', 'error' => $result->error]);

                return $sendRequest;
            }

            if (! $result->success) {
                $failures[] = $to.': '.$result->error;

                continue;
            }

            $lastReference = $result->providerReference;
        }

        if (count($failures) === count($recipients)) {
            $this->release($school, $sendRequest, implode('; ', $failures));

            return $sendRequest->refresh();
        }

        $sendRequest->update([
            'status' => 'sent',
            'provider_reference' => $lastReference,
            'error' => $failures ? implode('; ', $failures) : null,
        ]);

        return $sendRequest;
    }

    private function release(School $school, SmsSendRequest $sendRequest, ?string $error = null): void
    {
        DB::transaction(function () use ($school, $sendRequest, $error) {
            $wallet = $this->wallets->lockWalletFor($school);
            $settings = SmsGatewaySetting::query()->lockForUpdate()->firstOrCreate([]);

            $this->wallets->record($wallet, 'reversal', $sendRequest->credits_required, SmsSendRequest::class, $sendRequest->id, 'SMS send failed, credits released');
            $this->centralLedger->record($settings, 'reversal', $sendRequest->credits_required, SmsSendRequest::class, $sendRequest->id, 'SMS send failed, credits released');

            $sendRequest->update(['status' => 'failed', 'error' => $error]);
        });
    }
}
