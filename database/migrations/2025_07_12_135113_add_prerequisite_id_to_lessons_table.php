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
        Schema::table('lessons', function (Blueprint $table) {
            // Tambahkan kolom baru untuk menyimpan ID pelajaran prasyarat.
            // Kolom ini bisa null karena pelajaran pertama tidak punya prasyarat.
            $table->foreignId('prerequisite_id')->nullable()->after('order')->constrained('lessons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu sebelum menghapus kolom.
            $table->dropForeign(['prerequisite_id']);
            $table->dropColumn('prerequisite_id');
        });
    }
};