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
        Schema::table('contents', function (Blueprint $table) {
            // Tambahkan quiz_id, nullable karena tidak semua konten adalah kuis
            $table->foreignId('quiz_id')->nullable()->constrained('quizzes')->onDelete('set null'); // Jika kuis dihapus, konten tetap ada tapi tidak terhubung ke kuis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropForeign(['quiz_id']);
            $table->dropColumn('quiz_id');
        });
    }
};