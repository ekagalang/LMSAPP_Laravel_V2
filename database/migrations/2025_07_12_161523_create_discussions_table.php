<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa yang memulai diskusi
            $table->foreignId('content_id')->constrained()->onDelete('cascade'); // Diskusi ini milik konten mana
            $table->string('title'); // Judul topik diskusi
            $table->text('body'); // Isi dari topik diskusi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussions');
    }
};