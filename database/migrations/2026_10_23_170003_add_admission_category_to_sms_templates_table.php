<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE sms_templates MODIFY COLUMN category ENUM('guardian', 'hr', 'result', 'due_fee', 'attendance', 'birthday', 'dynamic', 'admission') NOT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE sms_templates MODIFY COLUMN category ENUM('guardian', 'hr', 'result', 'due_fee', 'attendance', 'birthday', 'dynamic') NOT NULL");
        }
    }
};
