<?php

namespace App\Services\Sms;

use App\Models\School;
use App\Models\SchoolSmsWallet;
use App\Models\SchoolSmsWalletTransaction;
use Illuminate\Support\Facades\DB;

class SchoolSmsWalletService
{
    public function walletFor(School $school): SchoolSmsWallet
    {
        return SchoolSmsWallet::withoutGlobalScope('school')->firstOrCreate(['school_id' => $school->id]);
    }

    public function lockWalletFor(School $school): SchoolSmsWallet
    {
        return SchoolSmsWallet::withoutGlobalScope('school')
            ->where('school_id', $school->id)
            ->lockForUpdate()
            ->firstOrCreate(['school_id' => $school->id]);
    }

    /**
     * Must be called from within an existing DB transaction, after locking the wallet row.
     */
    public function record(SchoolSmsWallet $wallet, string $type, int $signedCredits, ?string $referenceType = null, ?int $referenceId = null, ?string $note = null, ?int $createdBy = null): SchoolSmsWalletTransaction
    {
        $newBalance = $wallet->balance + $signedCredits;

        $transaction = SchoolSmsWalletTransaction::create([
            'school_id' => $wallet->school_id,
            'type' => $type,
            'credits' => $signedCredits,
            'balance_after' => $newBalance,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
            'created_by' => $createdBy,
        ]);

        $wallet->balance = $newBalance;

        if ($signedCredits > 0 && $type === 'purchase') {
            $wallet->total_purchased += $signedCredits;
        }

        if ($signedCredits < 0 && $type === 'usage') {
            $wallet->total_used += abs($signedCredits);
        }

        $wallet->save();

        return $transaction;
    }

    public function creditFromApprovedOrder(School $school, int $credits, string $referenceType, int $referenceId, ?int $createdBy = null): void
    {
        DB::transaction(function () use ($school, $credits, $referenceType, $referenceId, $createdBy) {
            $wallet = $this->lockWalletFor($school);
            $this->record($wallet, 'purchase', $credits, $referenceType, $referenceId, 'School credit purchase approved', $createdBy);
        });
    }
}
