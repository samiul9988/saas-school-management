<?php

namespace App\Services\Accounting;

use App\Models\ChartOfAccount;
use App\Models\School;

class ChartOfAccountSeeder
{
    public const CASH = '1000';

    public const BANK = '1010';

    public const STUDENT_FEE_INCOME = '4000';

    public const SALARY_EXPENSE = '5000';

    /**
     * @return array<int, array{code: string, name: string, type: string}>
     */
    public static function defaults(): array
    {
        return [
            ['code' => self::CASH, 'name' => 'Cash in Hand', 'type' => 'asset'],
            ['code' => self::BANK, 'name' => 'Bank Account', 'type' => 'asset'],
            ['code' => '1100', 'name' => 'Accounts Receivable - Student Fees', 'type' => 'asset'],
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability'],
            ['code' => '2100', 'name' => 'Salary Payable', 'type' => 'liability'],
            ['code' => '3000', 'name' => 'Capital / Fund', 'type' => 'equity'],
            ['code' => self::STUDENT_FEE_INCOME, 'name' => 'Tuition Fee Income', 'type' => 'income'],
            ['code' => '4010', 'name' => 'Admission Fee Income', 'type' => 'income'],
            ['code' => '4020', 'name' => 'Examination Fee Income', 'type' => 'income'],
            ['code' => '4030', 'name' => 'Donation Income', 'type' => 'income'],
            ['code' => '4090', 'name' => 'Miscellaneous Income', 'type' => 'income'],
            ['code' => self::SALARY_EXPENSE, 'name' => 'Salary Expense', 'type' => 'expense'],
            ['code' => '5010', 'name' => 'Utility Expense', 'type' => 'expense'],
            ['code' => '5020', 'name' => 'Rent Expense', 'type' => 'expense'],
            ['code' => '5030', 'name' => 'Maintenance Expense', 'type' => 'expense'],
            ['code' => '5040', 'name' => 'Supplies Expense', 'type' => 'expense'],
            ['code' => '5090', 'name' => 'Miscellaneous Expense', 'type' => 'expense'],
        ];
    }

    /**
     * Idempotent: only creates accounts that don't already exist for this school (by code).
     */
    public static function seedFor(School $school): void
    {
        $existingCodes = ChartOfAccount::withoutGlobalScope('school')
            ->where('school_id', $school->id)
            ->pluck('code')
            ->all();

        foreach (self::defaults() as $account) {
            if (in_array($account['code'], $existingCodes, true)) {
                continue;
            }

            ChartOfAccount::withoutGlobalScope('school')->create([
                'school_id' => $school->id,
                'code' => $account['code'],
                'name' => $account['name'],
                'type' => $account['type'],
                'is_system' => true,
                'is_active' => true,
            ]);
        }
    }
}
