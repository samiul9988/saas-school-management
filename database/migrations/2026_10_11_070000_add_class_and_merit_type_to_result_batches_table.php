<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('result_batches', function (Blueprint $table) {
            $table->foreignId('school_class_id')->nullable()->after('exam_id')->constrained('school_classes')->nullOnDelete();
            $table->string('merit_process_type')->nullable()->after('school_class_id');
        });
    }

    public function down(): void
    {
        Schema::table('result_batches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_class_id');
            $table->dropColumn('merit_process_type');
        });
    }
};
