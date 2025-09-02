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
        Schema::create('course_period_instructor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Ensure one instructor can't be assigned twice to the same period
            $table->unique(['course_period_id', 'user_id']);
            
            // Index for better performance
            $table->index(['course_period_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_period_instructor');
    }
};
