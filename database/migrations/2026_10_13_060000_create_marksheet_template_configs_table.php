<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marksheet_template_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->unsignedTinyInteger('template_number')->default(1);
            $table->string('marksheet_header')->default('ACADEMIC TRANSCRIPT');
            $table->string('mark_sheet_size')->default('Class Wise');
            $table->boolean('student_img_status')->default(true);
            $table->boolean('watermark_status')->default(false);
            $table->timestamps();

            $table->unique('school_class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marksheet_template_configs');
    }
};
