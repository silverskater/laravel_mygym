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
        Schema::create('scheduled_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_type_id')->constrained()->onDelete('cascade');
            $table->datetime('scheduled_at')->index();
            $table->integer('capacity')->unsigned()->default(20)->comment('Maximum number of participants');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled')->index();
            $table->string('location')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->index(['class_type_id', 'status'], 'idx_class_type_status');
            $table->index(['instructor_id', 'status'], 'idx_instructor_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_classes');
    }
};
