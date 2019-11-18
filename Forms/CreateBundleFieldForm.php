<?php
namespace Pingu\Field\Forms;

use Pingu\Entity\Contracts\BundleContract;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleField;
use Pingu\Forms\Support\Fields\Hidden;
use Pingu\Forms\Support\Fields\Link;
use Pingu\Forms\Support\Fields\NumberInput;
use Pingu\Forms\Support\Fields\Select;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class CreateBundleFieldForm extends Form
{
    protected $field;
    protected $action;

    /**
     * Bring variables in your form through the constructor :
     * 
     * @param BundleFieldContract $field
     * @param array               $url
     */
    public function __construct(BundleFieldContract $field, array $action = [])
    {
        $this->field = $field;
        $this->action = $action;
        parent::__construct();
    }

    /**
     * @return array
     */
    public function elements(): array
    {
        $fields = $this->field->fields()->toFormElements();

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
                ->attribute('required', true);
            return;
        }
        if ($fixedCardinality == -1) {
            $this->getElement('_cardinality_select')
                ->setValue('-1')
                ->attribute('disabled', true);
            $this->removeField('cardinality');
        } else {
            $this->getElement('_cardinality_select')
                ->setValue('number')
                ->attribute('disabled', true);
            $this->getElement('cardinality')
                ->attribute('disabled', true)
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
     * @see https://github.com/LaravelCollective/docs/blob/5.6/html.md
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