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
        Schema::table('course_periods', function (Blueprint $table) {
            $table->string('join_token', 50)->unique()->nullable()->after('max_participants');
            $table->index('join_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_periods', function (Blueprint $table) {
            $table->dropIndex(['join_token']);
            $table->dropColumn('join_token');
        });
    }
};
