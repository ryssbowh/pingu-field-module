<?php

namespace Pingu\Field\Events;

use Illuminate\Queue\SerializesModels;
use Pingu\Entity\Entities\Entity;
use Pingu\Field\Support\FieldRevision;

class RevisionCreated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Entity $entity, FieldRevision $revision)
    {
        $this->revision = $revision;
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
