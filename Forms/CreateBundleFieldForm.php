<?php
namespace Pingu\Field\Forms;

use Illuminate\Support\Collection;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Contracts\FieldContextContract;
use Pingu\Field\Entities\BundleField;
use Pingu\Forms\Support\Fields\Hidden;
use Pingu\Forms\Support\Fields\Link;
use Pingu\Forms\Support\Fields\Select;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class CreateBundleFieldForm extends Form
{
    /**
     * @var BundleFieldContract
     */
    protected $field;

    /**
     * @var array
     */
    protected $action;

    /**
     * @var Collection
     */
    protected $fields;

    /**
     * Bring variables in your form through the constructor :
     * 
     * @param BundleFieldContract $field
     * @param array               $url
     */
    public function __construct(BundleFieldContract $field, Collection $fields, array $action)
    {
        $this->field = $field;
        $this->action = $action;
        $this->fields = $fields;
        parent::__construct();
    }

    /**
     * @return array
     */
    public function elements(): array
    {
        $bundleField = $this->field;
        $fields = $this->fields->map(function ($field) use ($bundleField) {
            $value = $field->formValue($bundleField);
            return $field->toFormElement($value);
        })->all();

        $fields[] = new Hidden(
            '_field',
            [
                'default' => $this->field::uniqueName()
            ]
        );
        $fields[] = new Submit('_submit');

        return $fields;
    }

    protected function afterBuilt()
    {
        $fixedCardinality = $this->field->fixedCardinality();
        if ($fixedCardinality === false) {
            $this->getElement('cardinality')
                ->option('required', true);
            return;
        }
        if ($fixedCardinality == -1) {
            $this->getElement('_cardinality_select')
                ->setValue('-1')
                ->option('disabled', true);
            $this->removeField('cardinality');
        } else {
            $this->getElement('_cardinality_select')
                ->setValue('number')
                ->option('disabled', true);
            $this->getElement('cardinality')
                ->option('disabled', true)
                ->setValue($fixedCardinality);
        }
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
     * @see    https://github.com/LaravelCollective/docs/blob/5.6/html.md
     */
    public function action(): array
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
    public function name(): string
    {
        return 'create-bundle-field';
    }
}