<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('transport_route_id')->constrained('transport_routes')->cascadeOnDelete();
            $table->foreignId('route_stop_id')->nullable()->constrained('route_stops')->nullOnDelete();
            $table->decimal('monthly_fee', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_transports');
    }
};
