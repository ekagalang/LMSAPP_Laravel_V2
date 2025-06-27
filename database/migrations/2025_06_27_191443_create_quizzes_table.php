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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade'); // Kuis ini milik kursus mana
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Instruktur yang membuat kuis
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('total_marks')->default(0); // Total nilai maksimal kuis
            $table->integer('pass_marks')->default(0); // Nilai minimal untuk lulus
            $table->boolean('show_answers_after_attempt')->default(false); // Tampilkan jawaban setelah attempts
            $table->integer('time_limit')->nullable(); // Batas waktu dalam menit
            $table->enum('status', ['draft', 'published'])->default('draft'); // Status kuis
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};