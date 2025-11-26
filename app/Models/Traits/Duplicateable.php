<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait Duplicateable
{
    /**
     * Duplicate the model and its specified relationships recursively.
     *
     * @param bool $addCopyToTitle Whether to add "(Copy)" to the title (default: true)
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function duplicate($addCopyToTitle = true): Model
    {
        return DB::transaction(function () use ($addCopyToTitle) {
            try {
                // Replicate the model instance without its relations
                $newModel = $this->replicate();

                // ✅ FIX: Remove any attributes that don't exist in the database
                // Ini mencegah error "Unknown column" saat insert
                // Apply to all models to ensure no schema mismatch
                $tableName = $newModel->getTable();
                $tableColumns = \Schema::getColumnListing($tableName);
                $modelAttributes = array_keys($newModel->getAttributes());

                // Remove attributes that don't exist in database
                foreach ($modelAttributes as $attr) {
                    if (!in_array($attr, $tableColumns)) {
                        unset($newModel->$attr);
                        \Log::warning('Removed non-existent column from model', [
                            'model' => get_class($newModel),
                            'table' => $tableName,
                            'column' => $attr,
                        ]);
                    }
                }

                // Append "(Copy)" to the title if it exists and $addCopyToTitle is true
                if ($addCopyToTitle && isset($newModel->title)) {
                    $newModel->title .= ' (Copy)';
                }

                // ✅ FIX: Reset unique/token fields untuk Course model
                // Ini mencegah duplicate key error pada enrollment_token
                if ($this instanceof \App\Models\Course) {
                    $newModel->enrollment_token = null;
                    $newModel->token_enabled = false;
                    $newModel->token_expires_at = null;
                    $newModel->token_type = 'random'; // Reset to default

                    \Log::info('Reset token fields for duplicated course', [
                        'original_course_id' => $this->id,
                    ]);
                }

                // ✅ DEBUG: Log attributes sebelum push untuk melihat apa yang akan di-insert
                \Log::info('Model attributes before push()', [
                    'model_type' => get_class($this),
                    'attributes' => $newModel->getAttributes(),
                    'fillable' => $newModel->getFillable(),
                ]);

                // Save the replicated model to get a new ID
                $newModel->push();

                \Log::info('Model replicated successfully', [
                    'model_type' => get_class($this),
                    'original_id' => $this->id,
                    'new_id' => $newModel->id,
                    'title' => $newModel->title ?? 'N/A'
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to replicate base model', [
                    'model_type' => get_class($this),
                    'original_id' => $this->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
            
            // =================================================================
            // PERBAIKAN: Logika khusus untuk duplikasi relasi Quiz secara mendalam
            // =================================================================
            // Cek apakah model ini adalah Content dan memiliki Quiz
            if ($this instanceof \App\Models\Content && $this->quiz) {
                try {
                    \Log::info('Starting quiz duplication for content', [
                        'content_id' => $this->id,
                        'quiz_id' => $this->quiz->id,
                        'old_lesson_id' => $this->lesson_id,
                        'new_lesson_id' => $newModel->lesson_id,
                    ]);

                    $originalQuiz = $this->quiz;
                    $newQuiz = $originalQuiz->replicate();

                    // ✅ FIX: Remove any attributes that don't exist in quizzes table
                    $quizTableColumns = \Schema::getColumnListing('quizzes');
                    $quizAttributes = array_keys($newQuiz->getAttributes());
                    foreach ($quizAttributes as $attr) {
                        if (!in_array($attr, $quizTableColumns)) {
                            unset($newQuiz->$attr);
                            \Log::warning('Removed non-existent column from Quiz model', [
                                'column' => $attr,
                            ]);
                        }
                    }

                    // Hanya tambahkan "(Copy)" jika ini adalah duplikasi langsung, bukan child
                    if ($addCopyToTitle) {
                        $newQuiz->title .= ' (Copy)';
                    }
                    // ✅ FIX: Pertahankan user_id dari quiz original
                    // Ini penting agar authorization/policy tetap bekerja
                    // Quiz harus dimiliki oleh instructor yang sama, bukan admin yang duplikasi
                    $newQuiz->user_id = $originalQuiz->user_id;

                    // ✅ FIX CRITICAL: Update lesson_id ke lesson baru dari content baru
                    // Ini sangat penting untuk authorization - QuizPolicy mengecek quiz->lesson->course
                    // Tanpa ini, quiz akan menunjuk ke lesson LAMA dan participant course BARU tidak bisa akses (403)
                    //
                    // MASALAH: Saat Content di-duplicate, $newModel->lesson_id masih berisi lesson_id LAMA
                    // karena replicate() menyalin semua attributes. Foreign key baru di-set SETELAH quiz diduplikasi.
                    //
                    // SOLUSI SEMENTARA: Set ke lesson_id dari quiz original (yang masih lama)
                    // Nanti akan kita update di bagian bawah setelah Content foreign key di-update
                    $newQuiz->lesson_id = $originalQuiz->lesson_id;

                    // ✅ FIX CRITICAL: Ensure status is published
                    // Saat quiz diduplikasi, status harus tetap published agar bisa diakses participant
                    // QuizController mengecek status published sebelum allow access
                    if ($originalQuiz->status === 'published') {
                        $newQuiz->status = 'published';
                    }

                    $newQuiz->save();

                    // Hubungkan konten baru dengan kuis baru
                    $newModel->quiz_id = $newQuiz->id;
                    $newModel->save();

                    // Duplikasi setiap pertanyaan dan opsinya
                    $questionsCount = $originalQuiz->questions->count();
                    foreach ($originalQuiz->questions as $index => $originalQuestion) {
                        $newQuestion = $originalQuestion->replicate();
                        $newQuestion->quiz_id = $newQuiz->id;
                        $newQuestion->save();

                        $optionsCount = $originalQuestion->options->count();
                        foreach ($originalQuestion->options as $originalOption) {
                            $newOption = $originalOption->replicate();
                            $newOption->question_id = $newQuestion->id;
                            $newOption->save();
                        }
                    }

                    \Log::info('Quiz duplicated successfully', [
                        'original_quiz_id' => $originalQuiz->id,
                        'new_quiz_id' => $newQuiz->id,
                        'questions_count' => $questionsCount,
                        'original_lesson_id' => $originalQuiz->lesson_id,
                        'new_lesson_id' => $newQuiz->lesson_id,
                        'user_id' => $newQuiz->user_id,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to duplicate quiz', [
                        'content_id' => $this->id,
                        'quiz_id' => $this->quiz->id ?? 'N/A',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    throw $e;
                }
            }

            // =================================================================
            // PERBAIKAN: Logika khusus untuk duplikasi Essay Questions
            // =================================================================
            // Cek apakah model ini adalah Content dan memiliki Essay Questions
            if ($this instanceof \App\Models\Content && $this->allEssayQuestions()->count() > 0) {
                try {
                    $essayQuestionsCount = $this->allEssayQuestions()->count();
                    \Log::info('Starting essay questions duplication', [
                        'content_id' => $this->id,
                        'essay_questions_count' => $essayQuestionsCount,
                    ]);

                    // Duplikasi setiap essay question
                    foreach ($this->allEssayQuestions as $originalEssayQuestion) {
                        $newEssayQuestion = $originalEssayQuestion->replicate();
                        $newEssayQuestion->content_id = $newModel->id;
                        $newEssayQuestion->save();
                    }

                    \Log::info('Essay questions duplicated successfully', [
                        'content_id' => $this->id,
                        'new_content_id' => $newModel->id,
                        'questions_count' => $essayQuestionsCount,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to duplicate essay questions', [
                        'content_id' => $this->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    throw $e;
                }
            }
            // =================================================================
            // AKHIR PERBAIKAN
            // =================================================================

            // Load the relations we want to duplicate (untuk relasi lain)
            $this->load($this->getRelationsToDuplicate());

            foreach ($this->getRelations() as $relationName => $relation) {
                // Skip relasi quiz dan essay questions karena sudah ditangani secara khusus di atas
                if ($relationName === 'quiz' || $relationName === 'allEssayQuestions' || $relationName === 'essayQuestions') {
                    continue;
                }

                if (!in_array($relationName, $this->getRelationsToDuplicate())) {
                    continue;
                }

                try {
                    \Log::info('Duplicating relation', [
                        'model_type' => get_class($this),
                        'relation_name' => $relationName,
                    ]);

                    $relationType = get_class($this->{$relationName}());

                    if ($relationType === 'Illuminate\Database\Eloquent\Relations\BelongsToMany') {
                        $pivotData = $this->{$relationName}()->get()->pluck('id');
                        $newModel->{$relationName}()->sync($pivotData);
                        \Log::info('BelongsToMany relation duplicated', [
                            'relation_name' => $relationName,
                            'count' => $pivotData->count(),
                        ]);
                        continue;
                    }

                    if ($relation instanceof \Illuminate\Database\Eloquent\Collection) {
                        $relatedCount = $relation->count();
                        \Log::info('Duplicating collection relation', [
                            'relation_name' => $relationName,
                            'count' => $relatedCount,
                        ]);

                        foreach ($relation as $index => $relatedModel) {
                            try {
                                // Child models should not have "(Copy)" added to their titles
                                $newChild = $relatedModel->duplicate(false);

                                // ✅ CRITICAL FIX: Update foreign key manually setelah duplikasi
                                // Karena duplicate() sudah save record dengan foreign key lama,
                                // kita perlu update foreign key ke parent baru secara manual
                                $foreignKeyName = $newModel->{$relationName}()->getForeignKeyName();
                                $newChild->{$foreignKeyName} = $newModel->id;
                                $newChild->save();

                                // ✅ CRITICAL FIX: Update quiz->lesson_id setelah Content->lesson_id di-update
                                // Ini untuk fix 403 error saat peserta baru mengakses quiz di course hasil duplikasi
                                if ($relatedModel instanceof \App\Models\Content && $newChild->quiz) {
                                    $quiz = $newChild->quiz;
                                    $oldQuizLessonId = $quiz->lesson_id;
                                    $quiz->lesson_id = $newChild->lesson_id;
                                    $quiz->save();

                                    \Log::info('Updated quiz lesson_id after content foreign key update', [
                                        'quiz_id' => $quiz->id,
                                        'old_lesson_id' => $oldQuizLessonId,
                                        'new_lesson_id' => $quiz->lesson_id,
                                        'content_id' => $newChild->id,
                                    ]);
                                }
                            } catch (\Exception $e) {
                                \Log::error('Failed to duplicate child in collection', [
                                    'relation_name' => $relationName,
                                    'index' => $index,
                                    'child_id' => $relatedModel->id ?? 'N/A',
                                    'error' => $e->getMessage(),
                                ]);
                                throw $e;
                            }
                        }

                        \Log::info('Collection relation duplicated successfully', [
                            'relation_name' => $relationName,
                            'count' => $relatedCount,
                        ]);
                    }
                    elseif ($relation instanceof Model) {
                        // Child models should not have "(Copy)" added to their titles
                        $newRelatedModel = $relation->duplicate(false);
                        $foreignKeyName = $this->{$relationName}()->getForeignKeyName();
                        $newModel->{$foreignKeyName} = $newRelatedModel->id;
                        $newModel->save();

                        \Log::info('Single model relation duplicated', [
                            'relation_name' => $relationName,
                            'original_id' => $relation->id,
                            'new_id' => $newRelatedModel->id,
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to duplicate relation', [
                        'model_type' => get_class($this),
                        'relation_name' => $relationName,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    throw $e;
                }
            }

            return $newModel;
        });
    }

    /**
     * Get the relationships that should be duplicated.
     */
    protected function getRelationsToDuplicate(): array
    {
        return $this->duplicateRelations ?? [];
    }
}
