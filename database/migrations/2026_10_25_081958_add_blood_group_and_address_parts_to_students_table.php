<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('blood_group', 10)->nullable()->after('religion');
            $table->string('village')->nullable()->after('address');
            $table->string('post_office')->nullable()->after('village');
            $table->string('upazila_thana')->nullable()->after('post_office');
            $table->string('district')->nullable()->after('upazila_thana');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['blood_group', 'village', 'post_office', 'upazila_thana', 'district']);
        });
    }
};
