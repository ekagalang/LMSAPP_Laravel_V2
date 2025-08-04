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
        Schema::create('essay_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_id')->constrained()->onDelete('cascade'); // Menunjuk ke konten 'esai'
            $table->longText('answer'); // Jawaban dari peserta
            $table->string('status')->default('submitted'); // Status submission
            $table->unsignedInteger('score')->nullable(); // Skor dari instruktur
            $table->text('feedback')->nullable(); // Feedback dari instruktur
            $table->timestamp('graded_at')->nullable(); // Kapan dinilai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essay_submissions');
    }
};
