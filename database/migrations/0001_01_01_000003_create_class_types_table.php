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
            $table->string('name')->unique();
            $table->string('description');
            $table->integer('duration')->unsigned()->default(60)->comment('Duration in minutes');
            $table->integer('capacity')->unsigned()->default(20)->comment('Maximum number of participants');
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all'])->default('all')->index();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->string('color')->default('#4F46EA')->comment('Default color for the class type, can be used for UI representation.');
            $table->string('image')->nullable()->comment('Optional image for the class');
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
