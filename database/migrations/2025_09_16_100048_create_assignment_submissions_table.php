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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student who submitted
            $table->text('submission_text')->nullable(); // Text submission or notes
            $table->text('submission_link')->nullable(); // Link submission
            $table->json('file_paths')->nullable(); // Array of file paths
            $table->json('file_metadata')->nullable(); // File info (names, sizes, types)
            $table->enum('status', ['draft', 'submitted', 'late', 'graded', 'returned'])->default('draft');
            $table->datetime('submitted_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->decimal('grade', 5, 2)->nullable(); // Grade given
            $table->integer('points_earned')->nullable();
            $table->text('instructor_feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users'); // Instructor who graded
            $table->datetime('graded_at')->nullable();
            $table->json('grade_metadata')->nullable(); // Additional grading info
            $table->integer('attempt_number')->default(1); // For multiple attempts
            $table->timestamps();

            // Indexes
            $table->index(['assignment_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index('submitted_at');
            $table->index(['assignment_id', 'status']);

            // Unique constraint for one submission per user per assignment (unless multiple attempts allowed)
            $table->unique(['assignment_id', 'user_id', 'attempt_number'], 'unique_submission_attempt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
