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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade'); // Konten milik pelajaran mana
            $table->string('title');
            $table->enum('type', ['text', 'video', 'document', 'image', 'quiz'])->default('text'); // Tipe konten
            $table->text('body')->nullable(); // Untuk teks atau URL video/dokumen/gambar
            $table->string('file_path')->nullable(); // Untuk path file yang diupload (dokumen/gambar)
            $table->integer('order')->default(0); // Untuk urutan konten
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};