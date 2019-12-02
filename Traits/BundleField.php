<?php

namespace Pingu\Field\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Entity\Entities\Entity;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Contracts\FieldsValidator;
use Pingu\Field\Entities\BundleField as BundleFieldModel;
use Pingu\Field\Support\FormRepository\BundleFieldForms;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\FieldGroup;
use Pingu\Forms\Support\FormElement;
use Pingu\Forms\Support\FormRepository;

trait BundleField
{
    protected $entity;

    /**
     * Cast a value from a form to a model usable value
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    abstract protected function castSingleValue($value);

    /**
     * Cast a value from a model into a form usable format
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    abstract protected function singleFormValue($value);

    /**
     * Turn a value of this field into a FormElement
     * 
     * @return Field
     */
    abstract protected function toSingleFormField(): Field;

    /**
     * Default validation rules
     * 
     * @return string
     */
    abstract protected function defaultValidationRule(): string;

    /**
     * Sets the entity for this field
     * 
     * @param Entity $entity
     */
    public function setEntity(Entity $entity) 
    {
        $this->entity = $entity;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->field->name;
    }

    /**
     * @inheritDoc
     */
    public function machineName(): string
    {
        return $this->field->machineName;
    }

    /**
     * Forms instance for this field
     * 
     * @return FormRepository
     */
    public function forms(): FormRepository
    {
        return new BundleFieldForms($this);
    }
    
    /**
     * field relation
     * 
     * @return MorphOne
     */
    public function field()
    {
        return $this->morphOne(BundleFieldModel::class, 'instance');
    }

    /**
     * @inheritDoc
     */
    public function definesRelation()
    {
        return false;
    }

    /**
     * Bundle getter
     * 
     * @return BundleContract
     */
    public function bundle(): BundleContract
    {
        return $this->field->bundle();
    }

    /**
     * @inheritDoc
     */
    public static function uniqueName(): string
    {
        $baseName = class_basename(static::class);
        if (Str::startsWith($baseName, 'Field')) {
            $baseName = substr($baseName, 5);
        }
        return Str::snake($baseName);
    }

    /**
     * @inheritDoc
     */
    public static function friendlyName(): string 
    {
        $baseName = class_basename(static::class);
        if (Str::startsWith($baseName, 'Field')) {
            $baseName = substr($baseName, 5);
        }
        return explodeCamelCase($baseName);
    }

    /**
     * @inheritDoc
     */
    public function value(bool $casted = true)
    {
        $value = ($this->entity and $this->entity->exists) ? $this->entity->{$this->machineName()} : [];
        if ($casted) {
            return $value;
        }
        return $this->formValue($value);
    }

    /**
     * @inheritDoc
     */
    public function fixedCardinality()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        $value = Arr::wrap($value);
        $_this = $this;
        return array_map( 
            function ($item) use ($_this) {
                return $_this->castSingleValue($item);
            }, $value
        );
    }

    /**
     * @inheritDoc
     */
    public function formValue($value)
    {
        $value = Arr::wrap($value);
        $_this = $this;
        return array_map( 
            function ($item) use ($_this) {
                return $_this->singleFormValue($item);
            }, $value
        );
    }

    /**
     * @inheritDoc
     */
    public function cardinality(): int
    {
        return $this->field->cardinality;
    }

    /**
     * @inheritDoc
     */
    public function toFormElement(): FormElement
    {
        $values = $this->value(false);
        $fields = [];
        if ($values) {
            foreach ($values as $index => $value) {
                $field = $this->toSingleFormField($value);
                $field->option('multiple', true);
                $field->setIndex($index);
                $fields[] = $field;
            }
        } else {
            $field = $this->toSingleFormField(null);
            $field->option('multiple', true);
            $fields[] = $field;
        }
        $options = [
            'helper' => $this->field->helper,
            'label' => $this->name(),
        ];
        return new FieldGroup($this->machineName(), $options, $fields);
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        $cardinality = $this->cardinality();
        $size = $cardinality == -1 ? '' : '|max:'.$cardinality;
        return [
            $this->machineName() => 'array'.$size,
            $this->machineName().'.*' => $this->defaultValidationRule()
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationMessages(): array
    {
        return [];
    }
}