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
            $table->foreignId('instructor_id')->constrained('users');
            $table->foreignId('class_type_id')->constrained();
            $table->datetime('scheduled_at'); // Date and time when the class is scheduled
            $table->integer('capacity')->unsigned()->default(20); // Default capacity for classes
            $table->enum('status', ['scheduled', 'completed', 'canceled'])->default('scheduled'); // Status of the class
            $table->string('location')->nullable(); // Optional location for the class
            $table->string('description')->nullable(); // Optional description for the class
            $table->timestamps();
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
