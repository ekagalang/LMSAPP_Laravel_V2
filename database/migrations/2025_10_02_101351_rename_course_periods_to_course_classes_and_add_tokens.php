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
        // 1. Rename main table: course_periods -> course_classes
        Schema::rename('course_periods', 'course_classes');

        // 2. Rename pivot tables
        Schema::rename('course_period_instructor', 'course_class_instructor');
        Schema::rename('course_period_user', 'course_class_user');

        // 3. Update column names in chats table
        Schema::table('chats', function (Blueprint $table) {
            $table->renameColumn('course_period_id', 'course_class_id');
        });

        // 4. Add token columns to course_classes
        Schema::table('course_classes', function (Blueprint $table) {
            $table->string('enrollment_token', 20)->nullable()->unique()->after('max_participants');
            $table->string('class_code', 20)->nullable()->unique()->after('name');
            $table->boolean('token_enabled')->default(false)->after('enrollment_token');
            $table->timestamp('token_expires_at')->nullable()->after('token_enabled');
        });

        // 5. Add token columns to courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->string('enrollment_token', 20)->nullable()->unique()->after('status');
            $table->boolean('token_enabled')->default(false)->after('enrollment_token');
            $table->timestamp('token_expires_at')->nullable()->after('token_enabled');
        });

        // 6. Rename foreign key column in course_class_instructor (if needed)
        Schema::table('course_class_instructor', function (Blueprint $table) {
            $table->renameColumn('course_period_id', 'course_class_id');
        });

        // 7. Rename foreign key column in course_class_user
        Schema::table('course_class_user', function (Blueprint $table) {
            $table->renameColumn('course_period_id', 'course_class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse all changes

        // 7. Revert course_class_user column
        Schema::table('course_class_user', function (Blueprint $table) {
            $table->renameColumn('course_class_id', 'course_period_id');
        });

        // 6. Revert course_class_instructor column
        Schema::table('course_class_instructor', function (Blueprint $table) {
            $table->renameColumn('course_class_id', 'course_period_id');
        });

        // 5. Drop token columns from courses
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['enrollment_token', 'token_enabled', 'token_expires_at']);
        });

        // 4. Drop token columns from course_classes
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropColumn(['enrollment_token', 'class_code', 'token_enabled', 'token_expires_at']);
        });

        // 3. Revert chats table column
        Schema::table('chats', function (Blueprint $table) {
            $table->renameColumn('course_class_id', 'course_period_id');
        });

        // 2. Rename pivot tables back
        Schema::rename('course_class_user', 'course_period_user');
        Schema::rename('course_class_instructor', 'course_period_instructor');

        // 1. Rename main table back
        Schema::rename('course_classes', 'course_periods');
    }
};
