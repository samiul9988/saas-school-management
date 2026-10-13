<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('staff_serial')->nullable()->after('id');
            $table->string('gender')->nullable()->after('designation');
            $table->string('religion')->nullable()->after('gender');
            $table->string('blood_group')->nullable()->after('religion');
            $table->string('father_name')->nullable()->after('blood_group');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->date('date_of_birth')->nullable()->after('mother_name');
            $table->date('resignation_date')->nullable()->after('joining_date');
            $table->text('address')->nullable()->after('resignation_date');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'staff_serial', 'gender', 'religion', 'blood_group', 'father_name',
                'mother_name', 'date_of_birth', 'resignation_date', 'address',
            ]);
        });
    }
};
