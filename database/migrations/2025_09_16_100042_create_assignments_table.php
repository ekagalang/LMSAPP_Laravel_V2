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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('submission_type', ['file', 'link', 'both'])->default('file');
            $table->json('allowed_file_types')->nullable(); // Store allowed file extensions
            $table->bigInteger('max_file_size')->nullable(); // In bytes
            $table->integer('max_files')->default(1); // Maximum number of files
            $table->datetime('due_date')->nullable();
            $table->boolean('allow_late_submission')->default(false);
            $table->datetime('late_submission_until')->nullable();
            $table->decimal('late_penalty', 5, 2)->default(0); // Percentage penalty for late submission
            $table->integer('max_points')->default(100);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_to_students')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Instructor/Admin who created
            $table->json('metadata')->nullable(); // Additional settings
            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'show_to_students']);
            $table->index('due_date');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
