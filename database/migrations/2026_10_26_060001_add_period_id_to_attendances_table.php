<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('student_id', 'attendances_student_id_fk_index');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'date']);
            $table->foreignId('period_id')->nullable()->after('section_id')->constrained('periods')->nullOnDelete();
            $table->unique(['student_id', 'date', 'period_id'], 'attendances_student_date_period_unique');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendances_student_date_period_unique');
            $table->dropConstrainedForeignId('period_id');
            $table->unique(['student_id', 'date']);
            $table->dropIndex('attendances_student_id_fk_index');
        });
    }
};
