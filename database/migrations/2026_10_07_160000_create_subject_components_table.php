<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('exam_short_code_id')->constrained('exam_short_codes')->cascadeOnDelete();
            $table->decimal('total', 6, 2)->default(100);
            $table->decimal('pass_mark', 6, 2)->default(33);
            $table->decimal('acceptance', 6, 2)->default(1);
            $table->unsignedInteger('serial')->default(0);
            $table->timestamps();

            $table->unique(['subject_id', 'exam_short_code_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_components');
    }
};
