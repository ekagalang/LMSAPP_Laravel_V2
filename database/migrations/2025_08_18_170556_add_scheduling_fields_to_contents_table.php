<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Add scheduling fields for Zoom content
            $table->datetime('scheduled_start')->nullable()->after('order');
            $table->datetime('scheduled_end')->nullable()->after('scheduled_start');
            $table->boolean('is_scheduled')->default(false)->after('scheduled_end');
            $table->integer('timezone_offset')->nullable()->after('is_scheduled'); // UTC offset in minutes

            // Index for performance
            $table->index(['type', 'is_scheduled', 'scheduled_start'], 'idx_content_scheduling');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropIndex('idx_content_scheduling');
            $table->dropColumn(['scheduled_start', 'scheduled_end', 'is_scheduled', 'timezone_offset']);
        });
    }
};
