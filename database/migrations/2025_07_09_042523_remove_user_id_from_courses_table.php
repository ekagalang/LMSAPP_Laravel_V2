<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['user_id']);
            // Hapus kolomnya
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Jika perlu rollback, tambahkan kolomnya kembali
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }
};