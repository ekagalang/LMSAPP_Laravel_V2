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
        // Add token_type to courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->string('token_type', 20)->default('random')->after('token_enabled');
        });

        // Add token_type to course_classes table
        Schema::table('course_classes', function (Blueprint $table) {
            $table->string('token_type', 20)->default('random')->after('token_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove token_type from courses
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('token_type');
        });

        // Remove token_type from course_classes
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropColumn('token_type');
        });
    }
};
