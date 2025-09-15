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
        Schema::table('questions', function (Blueprint $table) {
            // Media support for quiz questions
            $table->string('media_type')->nullable()->after('comprehension_type'); // 'audio', 'video', 'image'
            $table->string('media_path')->nullable()->after('media_type'); // path to media file
            $table->json('media_metadata')->nullable()->after('media_path'); // duration, size, etc.

            // Enhanced question features
            $table->text('context')->nullable()->after('question_text'); // Additional context/instructions
            $table->integer('time_limit_seconds')->nullable()->after('media_metadata'); // Per-question time limit
            $table->boolean('show_media_controls')->default(true)->after('time_limit_seconds'); // Show play/pause controls

            // Index for better performance
            $table->index(['media_type', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['media_type', 'type']);
            $table->dropColumn([
                'media_type',
                'media_path',
                'media_metadata',
                'context',
                'time_limit_seconds',
                'show_media_controls'
            ]);
        });
    }
};