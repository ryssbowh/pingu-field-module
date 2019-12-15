<?php

namespace Pingu\Field\Listeners;

class DashifyMachineNameField
{
    /**
     *
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $form = $event->form;
        if ($form->getName() == 'create-bundle-field') {
            $field = $form->getElement('machineName');
            $field->classes->add('js-dashify');
            $field->option('data-dashifyfrom', 'name');
        }
    }
}
