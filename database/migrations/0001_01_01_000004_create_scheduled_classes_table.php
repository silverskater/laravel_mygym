<?php

use App\Models\ClassType;
use App\Models\User;
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
        Schema::create('scheduled_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'instructor_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ClassType::class)->constrained()->cascadeOnDelete();
            $table->datetime('scheduled_at')->index();
            $table->integer('capacity')->unsigned()->default(20)->comment('Maximum number of participants');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled')->index();
            $table->string('location')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            // Define composite indexes for efficient querying.
            $table->index(['instructor_id', 'status'], 'idx_instructor_status');
            $table->index(['class_type_id', 'status'], 'idx_class_type_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop dependent tables first to avoid foreign key constraint errors.
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('scheduled_classes');
    }
};
