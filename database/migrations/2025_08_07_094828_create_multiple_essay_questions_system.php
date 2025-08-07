<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Content;
use App\Models\EssaySubmission;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel essay_questions
        Schema::create('essay_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->integer('order')->default(0);
            $table->integer('max_score')->default(100);
            $table->timestamps();
        });

        // 2. Buat tabel essay_answers
        Schema::create('essay_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('essay_submissions')->onDelete('cascade');
            $table->foreignId('question_id')->nullable()->constrained('essay_questions')->onDelete('cascade');
            $table->longText('answer');
            $table->unsignedInteger('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        // 3. MIGRASI DATA EXISTING
        $this->migrateExistingData();

        // 4. Hapus kolom lama dari essay_submissions
        Schema::table('essay_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('essay_submissions', 'answer')) {
                $table->dropColumn('answer');
            }
            if (Schema::hasColumn('essay_submissions', 'score')) {
                $table->dropColumn('score');
            }
            if (Schema::hasColumn('essay_submissions', 'feedback')) {
                $table->dropColumn('feedback');
            }
        });
    }

    private function migrateExistingData()
    {
        // Pindahkan content essay lama ke sistem baru
        $essayContents = Content::where('type', 'essay')->get();
        
        foreach ($essayContents as $content) {
            if (!empty($content->description)) {
                // Buat question dari description content
                $question = \App\Models\EssayQuestion::create([
                    'content_id' => $content->id,
                    'question' => $content->description,
                    'order' => 1,
                    'max_score' => 100,
                ]);

                // Pindahkan submissions lama ke essay_answers
                $submissions = EssaySubmission::where('content_id', $content->id)->get();
                
                foreach ($submissions as $submission) {
                    if (!empty($submission->answer)) {
                        \App\Models\EssayAnswer::create([
                            'submission_id' => $submission->id,
                            'question_id' => $question->id,
                            'answer' => $submission->answer,
                            'score' => $submission->score,
                            'feedback' => $submission->feedback,
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Kembalikan kolom lama
        Schema::table('essay_submissions', function (Blueprint $table) {
            $table->longText('answer')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->text('feedback')->nullable();
        });

        Schema::dropIfExists('essay_answers');
        Schema::dropIfExists('essay_questions');
    }
};