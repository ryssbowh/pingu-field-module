<?php

namespace Pingu\Field\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Validation\Validator;
use Pingu\Field\Contracts\HasFieldsContract;

class FieldsValidatorBuilt
{
    use SerializesModels;

    public $validator,$model;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Validator $validator, HasFieldsContract $model)
    {
        $this->validator = $validator;
        $this->model = $model;
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
