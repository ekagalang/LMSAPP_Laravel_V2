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
            // Replicate the model instance without its relations
            $newModel = $this->replicate();

            // Append "(Copy)" to the title if it exists and $addCopyToTitle is true
            if ($addCopyToTitle && isset($newModel->title)) {
                $newModel->title .= ' (Copy)';
            }

            // Save the replicated model to get a new ID
            $newModel->push();
            
            // =================================================================
            // PERBAIKAN: Logika khusus untuk duplikasi relasi Quiz secara mendalam
            // =================================================================
            // Cek apakah model ini adalah Content dan memiliki Quiz
            if ($this instanceof \App\Models\Content && $this->quiz) {
                $originalQuiz = $this->quiz;
                $newQuiz = $originalQuiz->replicate();
                // Hanya tambahkan "(Copy)" jika ini adalah duplikasi langsung, bukan child
                if ($addCopyToTitle) {
                    $newQuiz->title .= ' (Copy)';
                }
                $newQuiz->save();

                // Hubungkan konten baru dengan kuis baru
                $newModel->quiz_id = $newQuiz->id;
                $newModel->save();

                // Duplikasi setiap pertanyaan dan opsinya
                foreach ($originalQuiz->questions as $originalQuestion) {
                    $newQuestion = $originalQuestion->replicate();
                    $newQuestion->quiz_id = $newQuiz->id;
                    $newQuestion->save();

                    foreach ($originalQuestion->options as $originalOption) {
                        $newOption = $originalOption->replicate();
                        $newOption->question_id = $newQuestion->id;
                        $newOption->save();
                    }
                }
            }

            // =================================================================
            // PERBAIKAN: Logika khusus untuk duplikasi Essay Questions
            // =================================================================
            // Cek apakah model ini adalah Content dan memiliki Essay Questions
            if ($this instanceof \App\Models\Content && $this->allEssayQuestions()->count() > 0) {
                // Duplikasi setiap essay question
                foreach ($this->allEssayQuestions as $originalEssayQuestion) {
                    $newEssayQuestion = $originalEssayQuestion->replicate();
                    $newEssayQuestion->content_id = $newModel->id;
                    $newEssayQuestion->save();
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
                
                $relationType = get_class($this->{$relationName}());

                if ($relationType === 'Illuminate\Database\Eloquent\Relations\BelongsToMany') {
                    $pivotData = $this->{$relationName}()->get()->pluck('id');
                    $newModel->{$relationName}()->sync($pivotData);
                    continue;
                }

                if ($relation instanceof \Illuminate\Database\Eloquent\Collection) {
                    foreach ($relation as $relatedModel) {
                        // Child models should not have "(Copy)" added to their titles
                        $newChild = $relatedModel->duplicate(false);
                        $newModel->{$relationName}()->save($newChild);
                    }
                }
                elseif ($relation instanceof Model) {
                    // Child models should not have "(Copy)" added to their titles
                    $newRelatedModel = $relation->duplicate(false);
                    $foreignKeyName = $this->{$relationName}()->getForeignKeyName();
                    $newModel->{$foreignKeyName} = $newRelatedModel->id;
                    $newModel->save();
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
