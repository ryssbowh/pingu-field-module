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
        $this->loadEmpty();
    }

    /**
     * Loads the revisions
     * 
     * @return RevisionRepository
     */
    public function load(): RevisionRepository
    {
        $this->revisions = $this->loadRevisions();
        if (!$this->revisions) {
            return $this->loadEmpty();
        }
        $this->current = Arr::first($this->revisions);
        $this->current->load();
        return $this;
    }

    /**
     * Loads an empty revision
     * 
     * @return RevisionRepository
     */
    public function loadEmpty(): RevisionRepository
    {
        $this->current = new FieldRevision($this->entity, collect(), 0);
        return $this;
    }

    /**
     * Gets all the revisions ids
     * 
     * @return array
     */
    public function ids(): array
    {
        return array_keys($this->revisions);
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
     * Soft deletes multiple revisions
     * 
     * @param array  $revisions
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
     * Soft deletes a revision
     * 
     * @param int    $revision
     * 
     * @return RevisionRepository
     */
    public function delete(int $revision): RevisionRepository
    {
        $revision = $this->get($revision);
        if ($revision->is($this->current)) {
            throw RevisionException::cantDeleteCurrent($revision->id(), $this->entity);
        }
        $revision->delete();
        unset($this->revisions[$revision->id()]);
        return $this;
    }

    /**
     * Hard deletes all values for an entity
     * 
     * @return FieldRevision
     */
    public function destroy()
    {
        $values = $this->entity->morphMany(BundleFieldValue::class, 'entity')->withTrashed()->get();
        foreach ($values as $value) {
            $value->forceDelete();
        }
        $this->revisions = [];
        return $this;
    }

    /**
     * Gets a revision by id
     * 
     * @param int    $id
     *
     * @throws RevisionException
     * @return FieldRevision
     */
    public function get(int $id): FieldRevision
    {
        if (!isset($this->revisions[$id])) {
            throw RevisionException::doesNotExist($this->entity, $id);
        }
        return $this->revisions[$id]->load();
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
        $this->revisions[$newId] = $newRevision;
        krsort($this->revisions);
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
        if ($size > $maxSize) {
            $toDelete = array_slice($revisionIds, $maxSize);
            $this->deleteMultiple($toDelete);
            return $toDelete;
        }
        return [];
    }

    /**
     * Loads all revisions for an entity.
     * Saves it in cache
     * 
     * @return array
     */
    protected function loadRevisions()
    {
        $entity = $this->entity;
        return \Field::getCache(
            $this->cacheKeyValues,
            $this->entity,
            function () use ($entity) {
                $values = $entity->morphMany(BundleFieldValue::class, 'entity')
                    ->orderBy('revision_id', 'DESC')
                    ->get()
                    ->groupBy('revision_id');
                $revisions = [];
                foreach ($values as $revisionId => $items) {
                    $revisions[$revisionId] = new FieldRevision($entity, $items, $revisionId);
                }
                return $revisions;
            }
        );
    }
}