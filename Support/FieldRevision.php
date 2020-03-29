<?php 

namespace Pingu\Field\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Pingu\Entity\Support\Entity;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Entities\BundleFieldValue;
use Pingu\Field\Events\CreatingRevision;
use Pingu\Field\Events\RevisionCreated;
use Pingu\User\Entities\User;

/**
 * Class to handle a set of values attached to an entity for a specific revision id
 */
class FieldRevision
{
    /**
     * @var Collection
     */
    protected $values;

    /**
     * Id of this revision
     * 
     * @var int
     */
    protected $id;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var Carbon
     */
    protected $created;

    /**
     * @var User
     */
    protected $createdBy;

    public function __construct(Entity $entity, Collection $values, int $id)
    {
        $this->entity = $entity;
        $this->values = $values;
        $this->id = $id;
        $first = $values->first()->first();
        $this->created = $first->created_at;
        $this->createdBy = $first->createdBy;
    }

    /**
     * get all the values
     * 
     * @return [type] [description]
     */
    public function values(): Collection
    {
        return $this->values;
    }

    /**
     * Get the value for a field
     * 
     * @param  string $name
     * @return ?array
     */
    public function value(string $name): ?array
    {
        if ($field = $this->values->get($name)) {
            return $field->pluck('value')->toArray();
        }
        return null;
    }

    /**
     * Id for this revision
     * 
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Deletes all the values of this revision
     * 
     * @return FieldRevision
     */
    public function delete(): FieldRevision
    {
        foreach ($this->values as $array) {
            foreach ($array as $revision) {
                $revision->delete();
            }
        }
        return $this;
    }

    /**
     * Checks if this revision is the same intance as another one
     * 
     * @param FieldRevision $revision
     * 
     * @return boolean                
     */
    public function is(FieldRevision $revision): bool
    {
        return $this->id === $revision->id();
    }

    /**
     * Get the entity associated to this revision
     * 
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function created()
    {
        return $this->created;
    }

    public function createdBy()
    {
        return $this->createdBy;
    }

    public function toArray()
    {
        return [
            'id' => $this->id(),
            'values' => $this->values(),
            'entity' => $this->entity,
            'created' => $this->created,
            'createdBy' => $this->createdBy
        ];
    }
}