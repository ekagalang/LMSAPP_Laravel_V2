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
        $database = DB::getDatabaseName();

        // =========================================================
        // ASSIGNMENTS
        // =========================================================
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) use ($database) {
                // created_by
                if (
                    Schema::hasColumn('assignments', 'created_by') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignments')
                        ->where('index_name', 'assignments_created_by_index')
                        ->exists()
                ) {
                    $table->index(['created_by'], 'assignments_created_by_index');
                }

                // is_active
                if (
                    Schema::hasColumn('assignments', 'is_active') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignments')
                        ->where('index_name', 'assignments_is_active_index')
                        ->exists()
                ) {
                    $table->index(['is_active'], 'assignments_is_active_index');
                }

                // show_to_students
                if (
                    Schema::hasColumn('assignments', 'show_to_students') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignments')
                        ->where('index_name', 'assignments_show_to_students_index')
                        ->exists()
                ) {
                    $table->index(['show_to_students'], 'assignments_show_to_students_index');
                }

                // due_date
                if (
                    Schema::hasColumn('assignments', 'due_date') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignments')
                        ->where('index_name', 'assignments_due_date_index')
                        ->exists()
                ) {
                    $table->index(['due_date'], 'assignments_due_date_index');
                }

                // composite: is_active + show_to_students
                if (
                    Schema::hasColumn('assignments', 'is_active') &&
                    Schema::hasColumn('assignments', 'show_to_students') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignments')
                        ->where('index_name', 'assignments_active_visible_composite_index')
                        ->exists()
                ) {
                    $table->index(
                        ['is_active', 'show_to_students'],
                        'assignments_active_visible_composite_index'
                    );
                }
            });
        }

        // =========================================================
        // ASSIGNMENT_SUBMISSIONS
        // =========================================================
        if (Schema::hasTable('assignment_submissions')) {
            Schema::table('assignment_submissions', function (Blueprint $table) use ($database) {
                // assignment_id
                if (
                    Schema::hasColumn('assignment_submissions', 'assignment_id') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignment_submissions')
                        ->where('index_name', 'assignment_submissions_assignment_id_index')
                        ->exists()
                ) {
                    $table->index(['assignment_id'], 'assignment_submissions_assignment_id_index');
                }

                // user_id
                if (
                    Schema::hasColumn('assignment_submissions', 'user_id') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignment_submissions')
                        ->where('index_name', 'assignment_submissions_user_id_index')
                        ->exists()
                ) {
                    $table->index(['user_id'], 'assignment_submissions_user_id_index');
                }

                // status
                if (
                    Schema::hasColumn('assignment_submissions', 'status') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignment_submissions')
                        ->where('index_name', 'assignment_submissions_status_index')
                        ->exists()
                ) {
                    $table->index(['status'], 'assignment_submissions_status_index');
                }

                // submitted_at
                if (
                    Schema::hasColumn('assignment_submissions', 'submitted_at') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignment_submissions')
                        ->where('index_name', 'assignment_submissions_submitted_at_index')
                        ->exists()
                ) {
                    $table->index(['submitted_at'], 'assignment_submissions_submitted_at_index');
                }

                // composite: assignment_id + user_id
                if (
                    Schema::hasColumn('assignment_submissions', 'assignment_id') &&
                    Schema::hasColumn('assignment_submissions', 'user_id') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'assignment_submissions')
                        ->where('index_name', 'assignment_submissions_assignment_user_composite_index')
                        ->exists()
                ) {
                    $table->index(
                        ['assignment_id', 'user_id'],
                        'assignment_submissions_assignment_user_composite_index'
                    );
                }
            });
        }

        // =========================================================
        // AUDIO_LESSONS
        // =========================================================
        if (Schema::hasTable('audio_lessons')) {
            Schema::table('audio_lessons', function (Blueprint $table) use ($database) {
                // is_active
                if (
                    Schema::hasColumn('audio_lessons', 'is_active') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'audio_lessons')
                        ->where('index_name', 'audio_lessons_is_active_index')
                        ->exists()
                ) {
                    $table->index(['is_active'], 'audio_lessons_is_active_index');
                }

                // content_type
                if (
                    Schema::hasColumn('audio_lessons', 'content_type') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'audio_lessons')
                        ->where('index_name', 'audio_lessons_content_type_index')
                        ->exists()
                ) {
                    $table->index(['content_type'], 'audio_lessons_content_type_index');
                }

                // difficulty_level
                if (
                    Schema::hasColumn('audio_lessons', 'difficulty_level') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'audio_lessons')
                        ->where('index_name', 'audio_lessons_difficulty_level_index')
                        ->exists()
                ) {
                    $table->index(['difficulty_level'], 'audio_lessons_difficulty_level_index');
                }

                // sort_order
                if (
                    Schema::hasColumn('audio_lessons', 'sort_order') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'audio_lessons')
                        ->where('index_name', 'audio_lessons_sort_order_index')
                        ->exists()
                ) {
                    $table->index(['sort_order'], 'audio_lessons_sort_order_index');
                }
            });
        }

        // =========================================================
        // COURSES
        // =========================================================
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) use ($database) {
                // status
                if (
                    Schema::hasColumn('courses', 'status') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'courses')
                        ->where('index_name', 'courses_status_index')
                        ->exists()
                ) {
                    $table->index(['status'], 'courses_status_index');
                }

                // user_id
                if (
                    Schema::hasColumn('courses', 'user_id') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'courses')
                        ->where('index_name', 'courses_user_id_index')
                        ->exists()
                ) {
                    $table->index(['user_id'], 'courses_user_id_index');
                }

                // created_at
                if (
                    Schema::hasColumn('courses', 'created_at') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'courses')
                        ->where('index_name', 'courses_created_at_index')
                        ->exists()
                ) {
                    $table->index(['created_at'], 'courses_created_at_index');
                }
            });
        }

        // =========================================================
        // LESSONS
        // =========================================================
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) use ($database) {
                // course_id
                if (
                    Schema::hasColumn('lessons', 'course_id') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'lessons')
                        ->where('index_name', 'lessons_course_id_index')
                        ->exists()
                ) {
                    $table->index(['course_id'], 'lessons_course_id_index');
                }

                // sort_order
                if (
                    Schema::hasColumn('lessons', 'sort_order') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'lessons')
                        ->where('index_name', 'lessons_sort_order_index')
                        ->exists()
                ) {
                    $table->index(['sort_order'], 'lessons_sort_order_index');
                }
            });
        }

        // =========================================================
        // CONTENTS
        // =========================================================
        if (Schema::hasTable('contents')) {
            Schema::table('contents', function (Blueprint $table) use ($database) {
                // lesson_id
                if (
                    Schema::hasColumn('contents', 'lesson_id') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'contents')
                        ->where('index_name', 'contents_lesson_id_index')
                        ->exists()
                ) {
                    $table->index(['lesson_id'], 'contents_lesson_id_index');
                }

                // type
                if (
                    Schema::hasColumn('contents', 'type') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'contents')
                        ->where('index_name', 'contents_type_index')
                        ->exists()
                ) {
                    $table->index(['type'], 'contents_type_index');
                }

                // sort_order
                if (
                    Schema::hasColumn('contents', 'sort_order') &&
                    ! DB::table('information_schema.statistics')
                        ->where('table_schema', $database)
                        ->where('table_name', 'contents')
                        ->where('index_name', 'contents_sort_order_index')
                        ->exists()
                ) {
                    $table->index(['sort_order'], 'contents_sort_order_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // CONTENTS
        if (Schema::hasTable('contents')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->dropIndex('contents_lesson_id_index');
                $table->dropIndex('contents_type_index');
                $table->dropIndex('contents_sort_order_index');
            });
        }

        // LESSONS
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropIndex('lessons_course_id_index');
                $table->dropIndex('lessons_sort_order_index');
            });
        }

        // COURSES
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropIndex('courses_status_index');
                $table->dropIndex('courses_user_id_index');
                $table->dropIndex('courses_created_at_index');
            });
        }

        // AUDIO_LESSONS
        if (Schema::hasTable('audio_lessons')) {
            Schema::table('audio_lessons', function (Blueprint $table) {
                $table->dropIndex('audio_lessons_is_active_index');
                $table->dropIndex('audio_lessons_content_type_index');
                $table->dropIndex('audio_lessons_difficulty_level_index');
                $table->dropIndex('audio_lessons_sort_order_index');
            });
        }

        // ASSIGNMENT_SUBMISSIONS
        if (Schema::hasTable('assignment_submissions')) {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                $table->dropIndex('assignment_submissions_assignment_id_index');
                $table->dropIndex('assignment_submissions_user_id_index');
                $table->dropIndex('assignment_submissions_status_index');
                $table->dropIndex('assignment_submissions_submitted_at_index');
                $table->dropIndex('assignment_submissions_assignment_user_composite_index');
            });
        }

        // ASSIGNMENTS
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
