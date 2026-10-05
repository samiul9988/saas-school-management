<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('sms_provider')->nullable()->after('subscription_ends_at');
            $table->string('sms_api_key')->nullable()->after('sms_provider');
            $table->string('sms_sender_id')->nullable()->after('sms_api_key');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['sms_provider', 'sms_api_key', 'sms_sender_id']);
        });
    }
};
