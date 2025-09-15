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
        Schema::table('questions', function (Blueprint $table) {
            // Update enum to include new types
            $table->enum('type', ['multiple_choice', 'true_false', 'fill_blank', 'listening_comprehension'])
                  ->change();

            // Add new fields for different question types
            $table->text('correct_answer')->nullable()->after('marks'); // For fill_blank
            $table->text('alternative_answers')->nullable()->after('correct_answer'); // For fill_blank (pipe-separated)
            $table->enum('comprehension_type', ['text', 'multiple_choice'])->nullable()->after('alternative_answers'); // For listening_comprehension
            $table->text('expected_answer')->nullable()->after('comprehension_type'); // For listening_comprehension
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['correct_answer', 'alternative_answers', 'comprehension_type', 'expected_answer']);

            // Revert enum back to original values
            $table->enum('type', ['multiple_choice', 'true_false'])->change();
        });
    }
};