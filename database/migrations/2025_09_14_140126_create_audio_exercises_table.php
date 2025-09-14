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
        Schema::create('audio_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audio_lesson_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('question');
            $table->enum('exercise_type', ['multiple_choice', 'fill_blank', 'speech_response', 'comprehension'])->default('multiple_choice');
            $table->json('options')->nullable(); // For multiple choice options
            $table->json('correct_answers'); // Store correct answers
            $table->integer('points')->default(10);
            $table->text('audio_cue')->nullable(); // Specific audio timestamp or instruction
            $table->integer('play_from_seconds')->default(0); // Start playing from specific second
            $table->integer('play_to_seconds')->nullable(); // End playing at specific second
            $table->json('speech_recognition_keywords')->nullable(); // Keywords for speech recognition
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_exercises');
    }
};
