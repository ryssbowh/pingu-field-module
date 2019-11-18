<?php

namespace Pingu\Field\Events;

use Illuminate\Queue\SerializesModels;
use Pingu\Field\Contracts\HasFields;

class FieldsValidationRulesRetrieved
{
    use SerializesModels;

    protected $rules, $object, $updating;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array &$rules, HasFields $object, bool $updating)
    {
        $this->rules = $rules;
        $this->object = $object;
        $this->updating = $updating;
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
