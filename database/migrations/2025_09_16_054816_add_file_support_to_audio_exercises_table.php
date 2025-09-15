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
        Schema::table('audio_exercises', function (Blueprint $table) {
            $table->text('explanation')->nullable()->after('points'); // Add explanation field first
            $table->string('image_file_path')->nullable()->after('explanation'); // For visual questions
            $table->string('audio_file_path')->nullable()->after('image_file_path'); // For specific audio clips
            $table->string('document_file_path')->nullable()->after('audio_file_path'); // For PDF/documents
            $table->json('file_metadata')->nullable()->after('document_file_path'); // Store file info (size, type, etc)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_exercises', function (Blueprint $table) {
            $table->dropColumn([
                'explanation',
                'image_file_path',
                'audio_file_path',
                'document_file_path',
                'file_metadata'
            ]);
        });
    }
};