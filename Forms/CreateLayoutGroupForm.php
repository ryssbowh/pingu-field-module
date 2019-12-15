<?php
namespace Pingu\Field\Forms;

use Pingu\Entity\Entities\Entity;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Fields\TextInput;
use Pingu\Forms\Support\Form;

class CreateLayoutGroupForm extends Form
{
    /**
     * Bring variables in your form through the constructor :
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
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
            new TextInput(
                'name',
                [
                    'label' => 'Create group',
                    'placeholder' => 'Name'
                ]
            ),
            new Submit()
        ];
    }

    /**
     * Method for this form, POST GET DELETE PATCH and PUT are valid
     * 
     * @return string
     */
    public function method(): string
    {
        return 'POST';
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
    public function action(): array
    {
        return ['url' => '/admin/form-layout/'.$this->entity->entityType()];
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
        return 'create-form-layout-group';
    }
}