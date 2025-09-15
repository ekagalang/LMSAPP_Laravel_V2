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
            $table->integer('audio_duration_seconds')->nullable()->after('file_path');
            $table->enum('audio_difficulty_level', ['beginner', 'intermediate', 'advanced'])->nullable()->after('audio_duration_seconds');
            $table->json('audio_metadata')->nullable()->after('audio_difficulty_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['audio_duration_seconds', 'audio_difficulty_level', 'audio_metadata']);
        });
    }
};
