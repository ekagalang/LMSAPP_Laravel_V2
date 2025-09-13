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
        Schema::create('video_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->onDelete('cascade');
            $table->enum('type', ['quiz', 'annotation', 'hotspot', 'overlay', 'pause']);
            $table->decimal('timestamp', 8, 2); // Waktu dalam detik (misal: 120.50)
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('data')->nullable(); // Data tambahan (pertanyaan quiz, posisi hotspot, dll)
            $table->json('position')->nullable(); // Posisi x,y untuk hotspot/overlay
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_interactions');
    }
};
