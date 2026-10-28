<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('result_batch_results', function (Blueprint $table) {
            $table->unsignedInteger('failed_subjects_count')->default(0)->after('is_passed');
        });
    }

    public function down(): void
    {
        Schema::table('result_batch_results', function (Blueprint $table) {
            $table->dropColumn('failed_subjects_count');
        });
    }
};
