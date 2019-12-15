<?php 

namespace Pingu\Field\Entities;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Entity\Entities\Entity;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Support\FormRepository\BundleFieldForms;
use Pingu\Field\Traits\HasWidgets;
use Pingu\Forms\Support\FieldGroup;
use Pingu\Forms\Support\FormElement;
use Pingu\Forms\Support\FormRepository;

abstract class BaseBundleField extends BaseModel implements BundleFieldContract
{
    use HasWidgets;

    protected $entity;

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
        return $this->morphOne(BundleField::class, 'instance');
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
        return friendly_classname($baseName);
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