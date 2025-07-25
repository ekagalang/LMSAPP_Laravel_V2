<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait Duplicateable
{
    /**
     * Duplicate the model and its specified relationships recursively.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function duplicate(): Model
    {
        return DB::transaction(function () {
            // Replicate the model instance without its relations
            $newModel = $this->replicate();

            // Append "(Copy)" to the title if it exists
            if (isset($newModel->title)) {
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
                $newQuiz->title .= ' (Copy)';
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
            // AKHIR PERBAIKAN
            // =================================================================

            // Load the relations we want to duplicate (untuk relasi lain)
            $this->load($this->getRelationsToDuplicate());

            foreach ($this->getRelations() as $relationName => $relation) {
                // Skip relasi quiz karena sudah ditangani secara khusus di atas
                if ($relationName === 'quiz') {
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
                        $newChild = $relatedModel->duplicate();
                        $newModel->{$relationName}()->save($newChild);
                    }
                }
                elseif ($relation instanceof Model) {
                    $newRelatedModel = $relation->duplicate();
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
