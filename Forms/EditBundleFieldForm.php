<?php
namespace Pingu\Field\Forms;

use Pingu\Field\Entities\BundleField;
use Pingu\Forms\Support\Fields\Hidden;
use Pingu\Forms\Support\Fields\Link;
use Pingu\Forms\Support\Fields\Submit;

class EditBundleFieldForm extends CreateBundleFieldForm
{
    /**
     * 
     * @return array
     */
    public function elements(): array
    {
        $fields = $this->field->fields()->toFormElements();
        $fields[] = new Submit;
        return $fields;
    }
    /**
     * Method for this form, POST GET DELETE PATCH and PUT are valid
     * 
     * @return string
     */
    public function method(): string
    {
        return 'PUT';
    }

    /**
     * Name for this form, ideally it would be application unique, 
     * best to prefix it with the name of the module it's for.
     * only alphanumeric and hyphens
     * 
     * @return string
     */
    public function name(): string
    {
        return 'edit-bundle-field';
    }

    protected function afterBuilt()
    {
        $this->getElement('machineName')->attribute('disabled', true);
    }
}