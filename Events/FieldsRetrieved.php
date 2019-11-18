<?php

namespace Pingu\Field\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Pingu\Field\Contracts\HasFields;

class FieldsRetrieved
{
    use SerializesModels;

    protected $fields, $object;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Collection $fields, HasFields $object)
    {
        $this->fields = $fields;
        $this->object = $object;
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
