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
        Schema::create('video_interaction_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_interaction_id')->constrained('video_interactions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('response_data'); // Jawaban user (pilihan, teks, dll)
            $table->boolean('is_correct')->nullable(); // Untuk quiz, apakah jawaban benar
            $table->integer('attempts')->default(1);
            $table->timestamp('answered_at');
            $table->timestamps();
            
            $table->unique(['video_interaction_id', 'user_id']); // User hanya bisa respond sekali per interaksi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_interaction_responses');
    }
};
