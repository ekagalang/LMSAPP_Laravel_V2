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
        Schema::table('reflections', function (Blueprint $table) {
            // Index for user's reflections (most common query)
            $table->index(['user_id', 'created_at'], 'idx_reflections_user_created');

            // Index for instructor filtering (visibility + requires_response)
            $table->index(['visibility', 'requires_response'], 'idx_reflections_visibility_response');

            // Index for analytics queries (mood distribution)
            $table->index(['mood', 'created_at'], 'idx_reflections_mood_created');

            // Index for response tracking (simplified to avoid TEXT column issues)
            $table->index(['requires_response', 'responded_by'], 'idx_reflections_response_tracking');

            // Index for instructor responses
            $table->index(['responded_by', 'responded_at'], 'idx_reflections_responded');

            // Composite index for common filters
            $table->index(['user_id', 'visibility', 'created_at'], 'idx_reflections_user_visibility_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reflections', function (Blueprint $table) {
            $table->dropIndex('idx_reflections_user_created');
            $table->dropIndex('idx_reflections_visibility_response');
            $table->dropIndex('idx_reflections_mood_created');
            $table->dropIndex('idx_reflections_response_tracking');
            $table->dropIndex('idx_reflections_responded');
            $table->dropIndex('idx_reflections_user_visibility_created');
        });
    }
};
