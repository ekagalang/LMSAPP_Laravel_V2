<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the enum column
        DB::statement("ALTER TABLE video_interactions MODIFY COLUMN type ENUM('quiz', 'reflection', 'annotation', 'hotspot', 'overlay', 'pause') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove reflection from enum
        DB::statement("ALTER TABLE video_interactions MODIFY COLUMN type ENUM('quiz', 'annotation', 'hotspot', 'overlay', 'pause') NOT NULL");
    }
};
