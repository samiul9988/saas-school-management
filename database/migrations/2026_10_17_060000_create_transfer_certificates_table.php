<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('serial')->unique();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('student_name');
            $table->string('address')->nullable();
            $table->string('class_name')->nullable();
            $table->string('roll')->nullable();
            $table->string('gpa')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('cause_of_leaving')->nullable();
            $table->string('examination_name')->nullable();
            $table->date('examination_date')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('session')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('promoted_class')->nullable();
            $table->date('issue_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_certificates');
    }
};
