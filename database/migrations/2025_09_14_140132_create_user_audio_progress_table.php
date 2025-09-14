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
        Schema::create('user_audio_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('audio_lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('audio_exercise_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('current_position_seconds')->default(0); // Audio playback position
            $table->boolean('completed')->default(false);
            $table->integer('score')->default(0);
            $table->integer('max_score')->default(0);
            $table->json('answers')->nullable(); // Store user answers
            $table->json('speech_attempts')->nullable(); // Store speech recognition attempts
            $table->integer('attempts_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'audio_lesson_id', 'audio_exercise_id'], 'unique_user_audio_exercise');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_audio_progress');
    }
};
