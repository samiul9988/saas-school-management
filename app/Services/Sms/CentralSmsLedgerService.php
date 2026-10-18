<?php

namespace App\Services\Sms;

use App\Models\CentralSmsLedger;
use App\Models\SmsGatewaySetting;

class CentralSmsLedgerService
{
    /**
     * Must be called from within an existing DB transaction, after locking the settings row.
     */
    public function record(SmsGatewaySetting $settings, string $type, int $signedCredits, ?string $referenceType = null, ?int $referenceId = null, ?string $note = null, ?int $createdBy = null): CentralSmsLedger
    {
        $newBalance = $settings->central_balance + $signedCredits;

        $ledger = CentralSmsLedger::create([
            'type' => $type,
            'credits' => $signedCredits,
            'balance_after' => $newBalance,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
            'created_by' => $createdBy,
        ]);

        $settings->update(['central_balance' => $newBalance]);

        return $ledger;
    }
}
