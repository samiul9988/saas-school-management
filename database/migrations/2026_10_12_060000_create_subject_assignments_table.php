<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->string('group_name')->nullable();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('subject_type')->default('Compulsory');
            $table->unsignedInteger('serial')->default(0);
            $table->unsignedInteger('merge_id')->default(0);
            $table->timestamps();

            $table->unique(['school_class_id', 'group_name', 'subject_id'], 'subject_assignment_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_assignments');
    }
};
