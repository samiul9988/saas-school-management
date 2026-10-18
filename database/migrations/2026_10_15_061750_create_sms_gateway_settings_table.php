<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('bulksmsbd');
            $table->text('api_key')->nullable();
            $table->string('sender_id')->nullable();
            $table->string('api_base_url')->nullable();
            $table->decimal('price_per_sms', 10, 4)->default(0);
            $table->unsignedInteger('low_balance_threshold')->nullable();
            $table->bigInteger('central_balance')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_gateway_settings');
    }
};
