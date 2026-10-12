<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['academic_years', 'school_classes', 'sections'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->unsignedInteger('serial')->default(0)->after('id');
                $blueprint->string('status')->default('enable')->after('serial');
            });
        }

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->unsignedInteger('serial')->default(0);
            $table->string('name');
            $table->string('status')->default('enable');
            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->unsignedInteger('serial')->default(0);
            $table->string('name');
            $table->string('status')->default('enable');
            $table->timestamps();
        });

        Schema::create('student_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->unsignedInteger('serial')->default(0);
            $table->string('name');
            $table->string('status')->default('enable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_categories');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('shifts');

        foreach (['academic_years', 'school_classes', 'sections'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn(['serial', 'status']);
            });
        }
    }
};
