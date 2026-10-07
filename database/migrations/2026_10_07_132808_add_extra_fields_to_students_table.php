<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('guardian_name');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('religion')->nullable()->after('gender');
            $table->string('group_name')->nullable()->after('section_id');
            $table->string('category')->nullable()->after('group_name');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'mother_name', 'religion', 'group_name', 'category']);
        });
    }
};
