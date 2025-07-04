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
        // Mengubah kolom 'type' di tabel 'contents'
        Schema::table('contents', function (Blueprint $table) {
            // Mengubah tipe kolom menjadi string (VARCHAR) dengan panjang 255
            // dan mengizinkannya untuk diubah.
            $table->string('type', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jika perlu, Anda bisa menambahkan logika untuk mengembalikan perubahan
        // Namun untuk kasus ini, kita bisa biarkan kosong atau kembalikan ke ENUM jika sebelumnya ENUM
        Schema::table('contents', function (Blueprint $table) {
            // Contoh jika sebelumnya adalah ENUM (sesuaikan jika berbeda)
            // $table->enum('type', ['text', 'video', 'document', 'image', 'quiz'])->change();
        });
    }
};