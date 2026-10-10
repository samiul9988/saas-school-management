<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_batch_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('result_batch_id')->constrained('result_batches')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->string('group_name')->nullable();
            $table->decimal('total_obtained', 8, 2)->default(0);
            $table->decimal('total_full', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->decimal('gpa', 4, 2)->nullable();
            $table->foreignId('grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->unsignedInteger('position')->nullable();
            $table->boolean('is_passed')->default(true);
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['result_batch_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_batch_results');
    }
};
