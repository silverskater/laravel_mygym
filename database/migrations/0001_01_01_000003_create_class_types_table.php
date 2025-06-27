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
        Schema::create('class_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->integer('duration')->unsigned()->default(60); // In minutes.
            $table->integer('capacity')->unsigned()->default(20); // Default capacity for classes.
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all'])->default('all'); // Class level.
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status of the class type.
            $table->string('color')->default('#4F46EA'); // Default color for the class type, can be used for UI representation.
            $table->string('image')->nullable(); // Optional image for the class
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_types');
    }
};
