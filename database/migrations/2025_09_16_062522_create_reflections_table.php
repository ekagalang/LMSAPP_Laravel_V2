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
        Schema::create('reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('mood', ['very_sad', 'sad', 'neutral', 'happy', 'very_happy'])->nullable();
            $table->json('tags')->nullable(); // Array of tags like ['learning', 'challenge', 'achievement']
            $table->enum('visibility', ['private', 'instructors_only', 'public'])->default('instructors_only');
            $table->boolean('requires_response')->default(false); // If participant wants instructor response
            $table->text('instructor_response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reflections');
    }
};
