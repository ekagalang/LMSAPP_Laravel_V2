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
        Schema::table('audio_lessons', function (Blueprint $table) {
            $table->string('video_file_path')->nullable()->after('audio_file_path'); // For video files
            $table->enum('content_type', ['audio', 'video', 'mixed'])->default('audio')->after('video_file_path'); // Content type
            $table->json('video_metadata')->nullable()->after('content_type'); // Video info (resolution, codec, etc)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_lessons', function (Blueprint $table) {
            $table->dropColumn([
                'video_file_path',
                'content_type',
                'video_metadata'
            ]);
        });
    }
};