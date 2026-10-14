<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_time_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('periods')->cascadeOnDelete();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->time('delay_time')->nullable();
            $table->time('process_time')->nullable();
            $table->string('send_sms_status')->default('present');
            $table->timestamps();

            $table->unique(['section_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_time_configs');
    }
};
