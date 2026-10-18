<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_sms_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained('schools')->cascadeOnDelete();
            $table->bigInteger('balance')->default(0);
            $table->unsignedBigInteger('total_purchased')->default(0);
            $table->unsignedBigInteger('total_used')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_sms_wallets');
    }
};
