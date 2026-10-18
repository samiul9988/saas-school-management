<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_credit_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credits');
            $table->decimal('cost', 10, 2);
            $table->string('provider_reference')->nullable();
            $table->foreignId('purchased_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('purchased_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_credit_purchases');
    }
};
