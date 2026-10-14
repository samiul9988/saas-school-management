<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_attendance_time_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->cascadeOnDelete();
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->time('delay_time')->nullable();
            $table->time('input_process_time')->nullable();
            $table->time('output_process_time')->nullable();
            $table->json('days')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendance_time_configs');
    }
};
