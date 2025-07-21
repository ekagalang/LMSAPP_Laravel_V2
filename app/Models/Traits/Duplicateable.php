<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait Duplicateable
{
    /**
     * Duplicate the model and its specified relationships.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function duplicate()
    {
        return DB::transaction(function () {
            // 1. Replicate the main model instance (shallow copy)
            $newModel = $this->replicate($this->getDoNotDuplicate());

            // Add '(Copy)' to the title or name if it exists
            if (isset($newModel->title)) {
                $newModel->title = $this->title . ' (Copy)';
            } elseif (isset($newModel->name)) {
                $newModel->name = $this->name . ' (Copy)';
            }
            
            // Handle file duplication if applicable
            if (isset($this->replicateFile) && $this->replicateFile && !empty($this->{$this->replicateFile})) {
                $originalPath = $this->{$this->replicateFile};
                if (Storage::disk('public')->exists($originalPath)) {
                    $newPath = 'thumbnails/' . uniqid() . '-' . basename($originalPath);
                    Storage::disk('public')->copy($originalPath, $newPath);
                    $newModel->{$this->replicateFile} = $newPath;
                }
            }

            $newModel->push(); // Save the replicated model to get a new ID

            // 2. Load and duplicate the relationships
            $this->load($this->getRelationsToDuplicate());

            foreach ($this->getRelations() as $relationName => $relatedItems) {
                if (!in_array($relationName, $this->getRelationsToDuplicate())) {
                    continue;
                }

                if ($relatedItems instanceof \Illuminate\Database\Eloquent\Collection) {
                    foreach ($relatedItems as $relatedItem) {
                        $newChild = $relatedItem->duplicate();
                        $newModel->{$relationName}()->save($newChild);
                    }
                } elseif ($relatedItems instanceof \Illuminate\Database\Eloquent\Model) {
                     $newChild = $relatedItems->duplicate();
                     $newModel->{$relationName}()->save($newChild);
                }
            }

            // 3. Sync BelongsToMany relationships
            foreach ($this->getRelationsToDuplicate() as $relationName) {
                $relation = $this->{$relationName}();
                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                    $newModel->{$relationName}()->sync($this->{$relationName}()->pluck('id')->toArray());
                }
            }

            return $newModel;
        });
    }

    /**
     * Get the relationships that should be duplicated.
     * This should be defined in the model using the trait.
     *
     * @return array
     */
    protected function getRelationsToDuplicate(): array
    {
        return $this->duplicateRelations ?? [];
    }

    /**
     * Get the attributes that should not be duplicated.
     *
     * @return array
     */
    protected function getDoNotDuplicate(): array
    {
        return $this->doNotDuplicate ?? [];
    }
}
