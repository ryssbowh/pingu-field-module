<?php

namespace Pingu\Field\Forms;

use Pingu\Forms\Support\Fields\Select;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class BundleFieldsForm extends Form
{

    /**
     * Bring variables in your form through the constructor :
     */
    public function __construct(array $action)
    {
        $this->action = $action;
        $this->availableFields = [];
        foreach (\Field::getRegisteredBundleFields() as $name => $class) {
            $this->availableFields[$name] = $class::friendlyName();
        }
        parent::__construct();
    }

    /**
     * Fields definitions for this form, classes used here
     * must extend Pingu\Forms\Support\Field
     * 
     * @return array
     */
    public function elements(): array
    {
        return [
            new Select(
                '_field', 
                [
                    'label' => 'Add new field',
                    'items' => $this->availableFields,
                    'allowNoValue' => false
                ]
            ),
            new Submit(
                'submit',
                [
                    'label' => 'Submit'
                ]
            )
        ];
    }

    /**
     * Method for this form, POST GET DELETE PATCH and PUT are valid
     * 
     * @return string
     */
    public function method(): string
    {
        return 'GET';
    }

    /**
     * Url for this form, valid values are
     * ['url' => '/foo.bar']
     * ['route' => 'login']
     * ['action' => 'MyController@action']
     * 
     * @return array
     * @see    https://github.com/LaravelCollective/docs/blob/5.6/html.md
     */
    public function action(): array
    {
        return $this->action;
    }

    /**
     * Name for this form, ideally it would be application unique, 
     * best to prefix it with the name of the module it's for.
     * 
     * @return string
     */
    public function name(): string
    {
        return 'add-bundle-field';
    }

}