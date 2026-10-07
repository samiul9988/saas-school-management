<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mark_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('mark_id')->constrained('marks')->cascadeOnDelete();
            $table->foreignId('subject_component_id')->constrained('subject_components')->cascadeOnDelete();
            $table->decimal('marks_obtained', 6, 2)->nullable();
            $table->timestamps();

            $table->unique(['mark_id', 'subject_component_id'], 'mark_component_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mark_components');
    }
};
