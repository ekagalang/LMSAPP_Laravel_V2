<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa yang membalas
            $table->foreignId('discussion_id')->constrained()->onDelete('cascade'); // Balasan untuk diskusi mana
            $table->text('body'); // Isi balasan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_replies');
    }
};