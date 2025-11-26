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
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'assignments')
                    ->where('index_name', 'assignments_created_by_index')
                    ->exists()
                ) {
                    $table->index(['created_by'], 'assignments_created_by_index');
                }

                // is_active
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'assignments')
                    ->where('index_name', 'assignments_is_active_index')
                    ->exists()
                ) {
                    $table->index(['is_active'], 'assignments_is_active_index');
                }

                // show_to_students
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'assignments')
                    ->where('index_name', 'assignments_show_to_students_index')
                    ->exists()
                ) {
                    $table->index(['show_to_students'], 'assignments_show_to_students_index');
                }

                // due_date
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'assignments')
                    ->where('index_name', 'assignments_due_date_index')
                    ->exists()
                ) {
                    $table->index(['due_date'], 'assignments_due_date_index');
                }

                // composite: is_active + show_to_students
                if (! DB::table('information_schema.statistics')
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
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'assignment_submissions')
                    ->where('index_name', 'assignment_submissions_assignment_id_index')
                    ->exists()
                ) {
                    $table->index(['assignment_id'], 'assignment_submissions_assignment_id_index');
                }

                // user_id
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'assignment_submissions')
                    ->where('index_name', 'assignment_submissions_user_id_index')
                    ->exists()
                ) {
                    $table->index(['user_id'], 'assignment_submissions_user_id_index');
                }

                // status
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'assignment_submissions')
                    ->where('index_name', 'assignment_submissions_status_index')
                    ->exists()
                ) {
                    $table->index(['status'], 'assignment_submissions_status_index');
                }

                // submitted_at
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'assignment_submissions')
                    ->where('index_name', 'assignment_submissions_submitted_at_index')
                    ->exists()
                ) {
                    $table->index(['submitted_at'], 'assignment_submissions_submitted_at_index');
                }

                // composite: assignment_id + user_id
                if (! DB::table('information_schema.statistics')
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
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'audio_lessons')
                    ->where('index_name', 'audio_lessons_is_active_index')
                    ->exists()
                ) {
                    $table->index(['is_active'], 'audio_lessons_is_active_index');
                }

                // content_type
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'audio_lessons')
                    ->where('index_name', 'audio_lessons_content_type_index')
                    ->exists()
                ) {
                    $table->index(['content_type'], 'audio_lessons_content_type_index');
                }

                // difficulty_level
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'audio_lessons')
                    ->where('index_name', 'audio_lessons_difficulty_level_index')
                    ->exists()
                ) {
                    $table->index(['difficulty_level'], 'audio_lessons_difficulty_level_index');
                }

                // sort_order
                if (! DB::table('information_schema.statistics')
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
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'courses')
                    ->where('index_name', 'courses_status_index')
                    ->exists()
                ) {
                    $table->index(['status'], 'courses_status_index');
                }

                // user_id
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'courses')
                    ->where('index_name', 'courses_user_id_index')
                    ->exists()
                ) {
                    $table->index(['user_id'], 'courses_user_id_index');
                }

                // created_at
                if (! DB::table('information_schema.statistics')
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
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'lessons')
                    ->where('index_name', 'lessons_course_id_index')
                    ->exists()
                ) {
                    $table->index(['course_id'], 'lessons_course_id_index');
                }

                // sort_order
                if (! DB::table('information_schema.statistics')
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
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'contents')
                    ->where('index_name', 'contents_lesson_id_index')
                    ->exists()
                ) {
                    $table->index(['lesson_id'], 'contents_lesson_id_index');
                }

                // type
                if (! DB::table('information_schema.statistics')
                    ->where('table_schema', $database)
                    ->where('table_name', 'contents')
                    ->where('index_name', 'contents_type_index')
                    ->exists()
                ) {
                    $table->index(['type'], 'contents_type_index');
                }

                // sort_order
                if (! DB::table('information_schema.statistics')
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
