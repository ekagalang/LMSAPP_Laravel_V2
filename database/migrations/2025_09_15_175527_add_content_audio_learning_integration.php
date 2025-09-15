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
            // Add audio learning integration fields
            $table->boolean('is_audio_learning')->default(false)->after('audio_metadata');
            $table->unsignedBigInteger('audio_lesson_id')->nullable()->after('is_audio_learning');

            // Add foreign key constraint
            $table->foreign('audio_lesson_id')->references('id')->on('audio_lessons')->onDelete('set null');
        });

        // Add field to audio_lessons to allow course integration
        Schema::table('audio_lessons', function (Blueprint $table) {
            $table->boolean('available_for_courses')->default(true)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropForeign(['audio_lesson_id']);
            $table->dropColumn(['is_audio_learning', 'audio_lesson_id']);
        });

        Schema::table('audio_lessons', function (Blueprint $table) {
            $table->dropColumn('available_for_courses');
        });
    }
};