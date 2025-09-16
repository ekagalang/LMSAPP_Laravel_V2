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
        // Only add essential indexes for performance optimization

        // Assignments table indexes (core for assignment system)
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->index(['created_by'], 'assignments_created_by_index');
                $table->index(['is_active'], 'assignments_is_active_index');
                $table->index(['show_to_students'], 'assignments_show_to_students_index');
                $table->index(['due_date'], 'assignments_due_date_index');
                $table->index(['is_active', 'show_to_students'], 'assignments_active_visible_composite_index');
            });
        }

        // Assignment submissions table indexes (core for assignment system)
        if (Schema::hasTable('assignment_submissions')) {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                $table->index(['assignment_id'], 'assignment_submissions_assignment_id_index');
                $table->index(['user_id'], 'assignment_submissions_user_id_index');
                $table->index(['status'], 'assignment_submissions_status_index');
                $table->index(['submitted_at'], 'assignment_submissions_submitted_at_index');
                $table->index(['assignment_id', 'user_id'], 'assignment_submissions_assignment_user_composite_index');
            });
        }

        // Audio lessons table indexes
        if (Schema::hasTable('audio_lessons')) {
            Schema::table('audio_lessons', function (Blueprint $table) {
                $table->index(['is_active'], 'audio_lessons_is_active_index');
                $table->index(['content_type'], 'audio_lessons_content_type_index');
                $table->index(['difficulty_level'], 'audio_lessons_difficulty_level_index');
                $table->index(['sort_order'], 'audio_lessons_sort_order_index');
            });
        }

        // Courses table indexes
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->index(['status'], 'courses_status_index');
                $table->index(['user_id'], 'courses_user_id_index');
                $table->index(['created_at'], 'courses_created_at_index');
            });
        }

        // Lessons table indexes
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->index(['course_id'], 'lessons_course_id_index');
                $table->index(['sort_order'], 'lessons_sort_order_index');
            });
        }

        // Contents table indexes
        if (Schema::hasTable('contents')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->index(['lesson_id'], 'contents_lesson_id_index');
                $table->index(['type'], 'contents_type_index');
                $table->index(['sort_order'], 'contents_sort_order_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order

        if (Schema::hasTable('contents')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->dropIndex('contents_lesson_id_index');
                $table->dropIndex('contents_type_index');
                $table->dropIndex('contents_sort_order_index');
            });
        }

        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropIndex('lessons_course_id_index');
                $table->dropIndex('lessons_sort_order_index');
            });
        }

        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropIndex('courses_status_index');
                $table->dropIndex('courses_user_id_index');
                $table->dropIndex('courses_created_at_index');
            });
        }

        if (Schema::hasTable('audio_lessons')) {
            Schema::table('audio_lessons', function (Blueprint $table) {
                $table->dropIndex('audio_lessons_is_active_index');
                $table->dropIndex('audio_lessons_content_type_index');
                $table->dropIndex('audio_lessons_difficulty_level_index');
                $table->dropIndex('audio_lessons_sort_order_index');
            });
        }

        if (Schema::hasTable('assignment_submissions')) {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                $table->dropIndex('assignment_submissions_assignment_id_index');
                $table->dropIndex('assignment_submissions_user_id_index');
                $table->dropIndex('assignment_submissions_status_index');
                $table->dropIndex('assignment_submissions_submitted_at_index');
                $table->dropIndex('assignment_submissions_assignment_user_composite_index');
            });
        }

        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropIndex('assignments_created_by_index');
                $table->dropIndex('assignments_is_active_index');
                $table->dropIndex('assignments_show_to_students_index');
                $table->dropIndex('assignments_due_date_index');
                $table->dropIndex('assignments_active_visible_composite_index');
            });
        }
    }
};