<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_short_codes', function (Blueprint $table) {
            $table->string('note')->nullable()->after('name');
            $table->decimal('default_total', 8, 2)->default(100)->after('note');
            $table->decimal('default_pass_mark', 8, 2)->default(0)->after('default_total');
            $table->decimal('default_acceptance', 8, 2)->default(1)->after('default_pass_mark');
        });
    }

    public function down(): void
    {
        Schema::table('exam_short_codes', function (Blueprint $table) {
            $table->dropColumn(['note', 'default_total', 'default_pass_mark', 'default_acceptance']);
        });
    }
};
