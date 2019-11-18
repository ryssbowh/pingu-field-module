<?php

namespace Pingu\Field\Forms;

use Pingu\Field\Entities\BundleField;
use Pingu\Forms\Support\Fields\Link;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class ConfirmBundleFieldDeletion extends Form
{
    /**
     * Bring variables in your form through the constructor :
     */
    public function __construct(BundleField $field, array $action)
    {
        $this->field = $field;
        $this->action = $action;
        parent::__construct();
    }

    /**
     * Fields definitions for this form, classes used here
     * must extend Pingu\Forms\Support\Field
     * 
     * @return array
     */
    public function elements()
    {
        return [
            'submit' => [
                'field' => Submit::class,
                'options' => [
                    'label' => 'Confirm'
                ]
            ]
        ];
    }

    /**
     * Method for this form, POST GET DELETE PATCH and PUT are valid
     * 
     * @return string
     */
    public function method()
    {
        return "DELETE";
    }

    /**
     * Url for this form, valid values are
     * ['url' => '/foo.bar']
     * ['route' => 'login']
     * ['action' => 'MyController@action']
     * 
     * @return array
     * @see https://github.com/LaravelCollective/docs/blob/5.6/html.md
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * Name for this form, ideally it would be application unique, 
     * best to prefix it with the name of the module it's for.
     * only alphanumeric and hyphens
     * 
     * @return string
     */
    public function name()
    {
        return 'delete-bundle-field';
    }

}