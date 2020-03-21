<?php

namespace Pingu\Field\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pingu\Entity\Entities\Entity;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Entities\BundleFieldValue;
use Pingu\Field\Entities\Revision;
use Pingu\Field\Events\CreatingRevision;
use Pingu\Field\Events\RevisionCreated;
use Pingu\Field\Exceptions\RevisionException;
use Pingu\Field\Support\BaseField;
use Pingu\Field\Support\FieldRevision;

/**
 * Class designed to handle a set of revisions attached to an entity
 */
class RevisionRepository
{
   
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * List of revisions
     * 
     * @var array
     */
    protected $revisions;

    /**
     * @var boolean
     */
    protected $loaded = false;

    public function __construct(Entity $entity)
    {
        $this->revisions = collect();
        $this->entity = $entity;
    }

    /**
     * Loads the revisions
     * 
     * @return RevisionRepository
     */
    public function load(): RevisionRepository
    {
        if ($this->loaded) {
            return $this;
        }
        $this->revisions = $this->loadRevisions();
        $this->loaded = true;
        return $this;
    }

    /**
     * Get a revison by id
     * 
     * @param int $id
     * 
     * @return FieldRevision
     */
    public function get(int $id): FieldRevision
    {
        if (!$this->has($id)) {
            throw RevisionException::doesNotExist($this->entity, $id);
        }
        return $this->revisions[$id];
    }

    /**
     * Gets all the revisions ids
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->revisions;
    }

    /**
     * Counts the revisions
     * 
     * @return int
     */
    public function count(): int
    {
        return $this->revisions->count();
    }

    /**
     * Deletes all revisions
     */
    public function destroy()
    {
        $this->deleteMultiple($this->revisions);
    }

    /**
     * Deletes multiple revisions
     * 
     * @param array $revisions
     * 
     * @return RevisionRepository
     */
    public function deleteMultiple(array $revisions): RevisionRepository
    {
        foreach ($revisions as $revisionId) {
            $this->delete($revisionId);
        }
        return $this;
    }

    /**
     * Deletes a revision
     * 
     * @param int $id
     * 
     * @return RevisionRepository
     */
    public function delete(int $id): RevisionRepository
    {
        $this->get($id)->delete();
        $this->forget($id);
        return $this;
    }

    /**
     * Does the revision exists
     * 
     * @param int $id
     * 
     * @return boolean
     */
    public function has(int $id)
    {
        return $this->revisions->has($id);
    }

    /**
     * Gets the latest revision id
     * 
     * @return int
     */
    public function getLastId(): int
    {
        if ($this->revisions->isEmpty()) {
            return 0;
        }
        return $this->revisions->first()->id();
    }

    /**
     * Saves the current revision as a new revision
     * 
     * @return FieldRevision
     */
    public function createRevision(): ?FieldRevision
    {
        $fields = $this->gatherFieldsToSave();
        if ($fields->isEmpty()) {
            return null;
        }
        $id = $this->getLastId() + 1;
        $models = collect();
        foreach ($fields as $field) {
            $method = 'createBundleFieldModel';
            if ($field instanceof BaseField) {
                $method = 'createBaseFieldModel';
            }
            $models->put($field->machineName(), $this->$method($id, $field));
        }
        event(new CreatingRevision($this->entity, $models, $id));
        $this->performCreate($models);
        $revision = new FieldRevision($this->entity, $models, $id);
        event(new RevisionCreated($this->entity, $revision));
        $this->revisions->put($id, $revision);
        $this->deleteOldRevisions();
        return $revision;
    }

    /**
     * Removes all fields that shouldn't be saved in a revision
     * 
     * @return Collection
     */
    protected function gatherFieldsToSave(): Collection
    {
        $fields = $this->entity->fields()->get();
        foreach ($this->entity->ignoreInRevisions() as $name) {
            $fields->forget($name);
        }
        return $fields;
    }

    /**
     * Perform revision creation
     * 
     * @param Collection $models
     */
    protected function performCreate(Collection $models)
    {
        foreach ($models as $collection) {
            foreach ($collection as $model) {
                $model->save();
            }
        }
    }

    /**
     * Creates a revision model for a bundle field
     * 
     * @param  int                 $id    
     * @param  BundleFieldContract $field
     * @return Collection
     */
    protected function createBundleFieldModel(int $id, BundleFieldContract $field): Collection
    {
        $out = collect();
        foreach ($field->formValue($this->entity) as $value) {
            $model = new Revision;
            $model->fill(
                [
                'value' => $value,
                'revision' => $id,
                'field' => $field->machineName()
                ]
            );
            $model->entity()->associate($this->entity);
            $out->push($model);
        }
        return $out;
    }

    /**
     * Creates a revision model for a base field
     * 
     * @param  int                 $id    
     * @param  BundleFieldContract $field
     * @return Collection
     */
    protected function createBaseFieldModel(int $id, BaseField $field): Collection
    {
        $model = new Revision;
        $model->fill(
            [
            'value' => $field->castToFormValue($this->entity->{$field->machineName()}),
            'revision' => $id,
            'field' => $field->machineName()
            ]
        );
        $model->entity()->associate($this->entity);
        return collect([$model]);
    }

    /**
     * Forgets a revision id
     * 
     * @param int $id
     */
    protected function forget(int $id)
    {
        $this->revisions->forget($id);
    }

    /**
     * Soft deletes old revisions. Number of revisions to keep is a config
     */
    protected function deleteOldRevisions()
    {
        $size = $this->count();
        $maxSize = config('field.numberRevisionsToKeep', 10);
        if ($maxSize == -1 or $size <= $maxSize) {
            return [];
        }
        $toDelete = $this->revisions->sortKeysDesc()->slice($maxSize);
        $this->deleteMultiple($toDelete->keys()->all());
    }

    /**
     * Loads all revision ids for an entity.
     * Will save result in cache
     * 
     * @return array
     */
    protected function loadRevisions(): Collection
    {
        $entity = $this->entity;
        return \Field::getRevisionCache(
            $this->entity, function () use ($entity) {
                $values = $entity->morphMany(Revision::class, 'entity')
                    ->orderBy('revision', 'DESC')
                    ->get()
                    ->groupBy(['revision', 'field']);
                $out = collect();
                foreach ($values as $id => $collection) {
                    $out->put($id, new FieldRevision($entity, $collection, $id));
                }
                return $out;
            }
        );
    }
}