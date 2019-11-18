<?php

namespace Pingu\Field\Listeners;


class DeleteBundleFields
{

    /**
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        foreach($event->bundle->entityBundleFields() as $field){
            $field->instance->delete();
            $field->delete();
        }
    }
}
