<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_group_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->decimal('application_fee', 10, 2)->nullable();
            $table->unsignedInteger('serial')->default(0);
            $table->string('status')->default('enable');
            $table->timestamps();

            $table->unique(['school_class_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_group_assignments');
    }
};
