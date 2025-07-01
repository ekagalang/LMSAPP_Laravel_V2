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
        Schema::table('quizzes', function (Blueprint $table) {
            // Drop foreign key constraint first if it exists
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');

            // Add lesson_id
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Drop lesson_id
            // Tambahkan pengecekan ifExists untuk mencegah error jika foreign key tidak ada
            $table->dropForeign(['quizzes_lesson_id_foreign']);
            $table->dropColumn('lesson_id');

            // Re-add course_id
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
        });
    }
};