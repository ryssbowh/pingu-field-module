<?php

namespace Pingu\Field\Events;

use Illuminate\Queue\SerializesModels;
use Pingu\Field\Contracts\HasFieldsContract;

class FieldsValidationMessagesRetrieved
{
    use SerializesModels;

    protected $messages, $object;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array &$messages, HasFieldsContract $object)
    {
        $this->messages = $messages;
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
