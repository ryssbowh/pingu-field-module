<?php

namespace Pingu\Field\Support;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Pingu\Entity\Contracts\HasBundleContract;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Entities\BundleFieldValue;
use Pingu\Field\Exceptions\RevisionException;

/**
 * Class designed to handle a set of revisions attached to an entity
 */
class RevisionRepository
{   
    /**
     * @var HasBundleContract
     */
    protected $entity;

    /**
     * List of revisions
     * 
     * @var array
     */
    protected $revisions = [];

    /**
     * Current revision
     * 
     * @var FieldRevision
     */
    protected $current;

    /**
     * Cache key used for revision values
     * 
     * @var string
     */
    protected $cacheKeyValues = 'values';

    public function __construct(HasBundleContract $entity)
    {
        $this->entity = $entity;
        $this->current = new FieldRevision($this->entity, collect(), 0);
    }

    /**
     * Loads the revisions
     * 
     * @return RevisionRepository
     */
    public function load(): RevisionRepository
    {
        $this->revisions = $this->loadRevisionIds();
        if ($this->revisions) {
            $this->current = $this->loadRevision(Arr::first($this->revisions));
        }
        return $this;
    }

    /**
     * Alias for revisions
     * 
     * @return array
     */
    public function ids(): array
    {
        return $this->revisions();
    }

    /**
     * Gets all the revisions ids
     * 
     * @return array
     */
    public function revisions(): array
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
        return sizeof($this->revisions);
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
     * @param array  $revisions
     * 
     * @return RevisionRepository
     */
    public function deleteMultiple(array $revisions, $soft = true): RevisionRepository
    {
        foreach ($revisions as $revisionId) {
            $this->delete($revisionId, $soft);
        }
        return $this;
    }

    /**
     * Deletes a revision
     * 
     * @param int    $id
     * 
     * @return RevisionRepository
     */
    public function delete(int $id, $soft = true): RevisionRepository
    {
        if ($this->current->id() == $revision) {
            throw RevisionException::cantDeleteCurrent($id, $this->entity);
        }
        if (!$this->has($id)) {
            throw RevisionException::doesNotExist($this->entity, $id);
        }
        $method = $soft ? 'softDelete' : 'forceDelete';
        $this->$method($id);
        return $this;
    }

    /**
     * Soft deletes a revision values 
     * 
     * @param int $id
     */
    protected function softDelete(int $id)
    {
        $values = $this->getRevisionValues($id);
        foreach ($values as $value) {
            $value->delete();
        }
    }

    /**
     * Force deletes a revision values 
     * 
     * @param int $id
     */
    protected function forceDelete(int $id)
    {
        $values = $this->getRevisionValues($id);
        foreach ($values as $value) {
            $value->forceDelete();
        }
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
        return in_array($id, $this->revisions);
    }

    /**
     * Forgets a revision id
     * 
     * @param int $id
     */
    protected function forget(int $id)
    {
        $ind = array_search($id, $this->revisions);
        unset($this->revisions[$ind]);
    }

    /**
     * Saves the current revision as a new revision
     * 
     * @return FieldRevision
     */
    public function save(): FieldRevision
    {
        return $this->duplicateAndSetCurrent($this->current);
    }

    /**
     * Restores a revision by id
     * 
     * @param int    $id
     * 
     * @return FieldRevision
     */
    public function restore(int $id): FieldRevision
    {
        if ($id === $this->id()) {
            return $this->current;
        }
        $revision = $this->get($id);
        return $this->duplicateAndSetCurrent($revision);
    }

    /**
     * Forward all calls to the current revision
     * 
     * @param $method 
     * @param $args 
     * 
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->current, $method], $args);
    }

    /**
     * Gets current revision
     * 
     * @return FieldRevision
     */
    public function current(): FieldRevision
    {
        return $this->current;
    }

    /**
     * Duplicates a revision and sets it as the current one
     * 
     * @param FieldRevision $revision
     * 
     * @return FieldRevision
     */
    protected function duplicateAndSetCurrent(FieldRevision $revision): FieldRevision
    {
        $newId = $this->id() + 1;
        $newRevision = $revision->saveAsNew($newId);
        $revision->delete();
        array_unshift($this->revisions, $newId);
        $this->current = $newRevision;
        $this->deleteOldRevisions();
        return $newRevision;
    }

    /**
     * Soft deletes old revisions. Number of revisions to keep is a config
     * 
     * @return array
     */
    protected function deleteOldRevisions(): array
    {
        $revisionIds = $this->ids();
        $size = $this->count();
        $maxSize = config('field.numberRevisionsToKeep', 10);
        if ($size == -1) return [];
        if ($size > $maxSize) {
            $toDelete = array_slice($revisionIds, $maxSize);
            $this->deleteMultiple($toDelete, false);
            return $toDelete;
        }
        return [];
    }

    /**
     * Get values for a revision.
     * Will save result in cache
     * 
     * @param  int $id
     * @return Collection
     */
    protected function getRevisionValues(int $id): Collection
    {
        $entity = $this->entity;
        return \Field::getRevisionCache('values.'.$id, $this->entity, function () use ($entity, $id) {
            return $entity->morphMany(BundleFieldValue::class, 'entity')
                ->withTrashed()
                ->where('revision_id', $id)
                ->get();   
            }
        );
    }

    /**
     * Loads a revision
     * 
     * @param  int    $id
     * @return FieldRevision
     */
    public function loadRevision(int $id): FieldRevision
    {
        $values = $this->getRevisionValues($id);
        if ($values->isEmpty()) {
            throw RevisionException::doesNotExist($this->entity, $id);
        }
        $revision = new FieldRevision($this->entity, $values, $id);
        return $revision->load();
    }

    /**
     * Loads all revision ids for an entity.
     * Will save result in cache
     * 
     * @return array
     */
    protected function loadRevisionIds(): array
    {
        $entity = $this->entity;
        return \Field::getRevisionCache('ids', $this->entity, function () use ($entity) {
            return $this->entity->morphMany(BundleFieldValue::class, 'entity')
                ->withTrashed()
                ->orderBy('revision_id', 'DESC')
                ->get()
                ->groupBy('revision_id')
                ->keys()
                ->toArray();
            }
        );
    }
}