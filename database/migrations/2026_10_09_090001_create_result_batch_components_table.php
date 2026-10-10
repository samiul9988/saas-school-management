<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_batch_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('result_batch_id')->constrained('result_batches')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->decimal('weight', 5, 2)->default(100);
            $table->timestamps();

            $table->unique(['result_batch_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_batch_components');
    }
};
