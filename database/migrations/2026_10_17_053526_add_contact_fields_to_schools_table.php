<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('admin_name')->nullable()->after('name');
            $table->string('contact_number')->nullable()->after('phone');
            $table->foreignId('academic_year_id')->nullable()->after('plan_id')->constrained('academic_years')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_year_id');
            $table->dropColumn(['admin_name', 'contact_number']);
        });
    }
};
