<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admit_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->string('instruction_1')->nullable();
            $table->string('instruction_2')->nullable();
            $table->string('instruction_3')->nullable();
            $table->string('instruction_4')->nullable();
            $table->timestamps();

            $table->unique(['school_class_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admit_instructions');
    }
};
