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
        Schema::create('Youtubes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained('quiz_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('option_id')->nullable()->constrained('options')->onDelete('cascade'); // Untuk pilihan ganda
            $table->text('answer_text')->nullable(); // Untuk jawaban tipe teks (jika ada, untuk saat ini hanya MCQ/TrueFalse)
            $table->boolean('is_correct')->nullable(); // Apakah jawaban user benar untuk pertanyaan ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Youtubes');
    }
};