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

            // Load the relations we want to duplicate
            $this->load($this->getRelationsToDuplicate());

            foreach ($this->getRelations() as $relationName => $relation) {
                // Skip if the relation is not in our duplicate list
                if (!in_array($relationName, $this->getRelationsToDuplicate())) {
                    continue;
                }
                
                $relationType = get_class($this->{$relationName}());

                // ✅ BARU: Handle BelongsToMany relations (e.g., Course -> Instructors)
                if ($relationType === 'Illuminate\Database\Eloquent\Relations\BelongsToMany') {
                    // We don't duplicate the related models (Users), just the relationship.
                    $pivotData = $this->{$relationName}()->get()->pluck('id');
                    $newModel->{$relationName}()->sync($pivotData);
                    continue; // Continue to next relation
                }

                // For HasMany relations (e.g., a Course has many Lessons)
                if ($relation instanceof \Illuminate\Database\Eloquent\Collection) {
                    foreach ($relation as $relatedModel) {
                        $newChild = $relatedModel->duplicate(); // Recursive call
                        $newModel->{$relationName}()->save($newChild);
                    }
                }
                // For BelongsTo or HasOne relations (e.g., a Content has one Quiz)
                elseif ($relation instanceof Model) {
                    $newRelatedModel = $relation->duplicate(); // Recursive call
                    
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
