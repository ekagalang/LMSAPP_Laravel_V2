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
            $table->boolean('attendance_required')->default(false)->after('is_scheduled');
            $table->integer('min_attendance_minutes')->nullable()->after('attendance_required');
            $table->text('attendance_notes')->nullable()->after('min_attendance_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['attendance_required', 'min_attendance_minutes', 'attendance_notes']);
        });
    }
};
