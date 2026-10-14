<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'leave'])->default('present');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->enum('in_status', ['on_time', 'late'])->nullable();
            $table->enum('out_status', ['on_time', 'early'])->nullable();
            $table->string('leave_reason')->nullable();
            $table->string('leave_for')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
