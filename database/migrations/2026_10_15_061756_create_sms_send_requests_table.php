<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_send_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('idempotency_key')->unique();
            $table->json('recipients');
            $table->text('message');
            $table->string('context')->nullable();
            $table->unsignedInteger('segments');
            $table->unsignedBigInteger('credits_required');
            $table->enum('status', ['reserved', 'sent', 'failed', 'unknown', 'released'])->default('reserved');
            $table->string('provider_reference')->nullable();
            $table->text('error')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_send_requests');
    }
};
