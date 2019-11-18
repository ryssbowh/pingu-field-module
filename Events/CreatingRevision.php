<?php

namespace Pingu\Field\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Pingu\Entity\Entities\Entity;

class CreatingRevision
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Entity $entity, Collection $values, int $id)
    {
        $this->id = $id;
        $this->values = $values;
        $this->antity = $entity;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
