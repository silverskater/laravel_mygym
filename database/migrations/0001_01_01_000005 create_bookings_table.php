<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('scheduled_class_id')->constrained()->onDelete('cascade');
            $table->dateTime('booking_time');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'scheduled_class_id'], 'unique_booking_per_user_and_class');
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index(['scheduled_class_id', 'status'], 'idx_scheduled_class_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
