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
            // Tambah kolom passing_percentage (default 70%)
            $table->integer('passing_percentage')->default(70)->after('enable_leaderboard');

            // Hapus kolom total_marks dan pass_marks (akan digantikan dengan sistem persentase)
            $table->dropColumn(['total_marks', 'pass_marks']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Kembalikan kolom lama
            $table->integer('total_marks')->default(100);
            $table->integer('pass_marks')->default(70);

            // Hapus kolom baru
            $table->dropColumn('passing_percentage');
        });
    }
};
