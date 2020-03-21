<?php 

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Entity\Entities\Entity;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleFieldValue;
use Pingu\Field\Support\FormRepository\BundleFieldForms;
use Pingu\Field\Traits\HasFilterWidgets;
use Pingu\Field\Traits\HasWidgets;
use Pingu\Forms\Contracts\FormRepositoryContract;
use Pingu\Forms\Support\FieldGroup;
use Pingu\Forms\Support\FormElement;

abstract class BaseBundleField extends BaseModel implements BundleFieldContract
{
    use HasWidgets, HasFilterWidgets;

    protected $with = ['field'];

    /**
     * @inheritDoc
     */
    public function getNameAttribute(): string
    {
        return $this->field->name;
    }

    /**
     * @inheritDoc
     */
    public function getMachineNameAttribute(): string
    {
        return $this->field->machineName;
    }

    /**
     * @inheritDoc
     */
    public function getCardinalityAttribute(): string
    {
        return $this->field->cardinality;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->getNameAttribute();
    }

    /**
     * @inheritDoc
     */
    public function machineName(): string
    {
        return $this->getMachineNameAttribute();
    }

    /**
     * @inheritDoc
     */
    public function cardinality(): int
    {
        return $this->getCardinalityAttribute();
    }

    /**
     * Forms instance for this field
     * 
     * @return FormRepositoryContract
     */
    public function forms(): FormRepositoryContract
    {
        return new BundleFieldForms($this);
    }
    
    /**
     * field relation
     * 
     * @return MorphOne
     */
    public function field(): MorphOne
    {
        return $this->morphOne(BundleField::class, 'instance');
    }

    /**
     * value relation
     * 
     * @return HasOne
     */
    public function value(): HasOne
    {
        return $this->hasOne(BundleFieldValue::class, 'field_id');
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
    public function fixedCardinality()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function castValueToDb($value)
    {
        $value = Arr::wrap($value);
        $_this = $this;
        return array_map( 
            function ($item) use ($_this) {
                return $_this->castSingleValueToDb($item);
            }, $value
        );
    }

    /**
     * @inheritDoc
     */
    public function castToFormValue($value)
    {
        $value = Arr::wrap($value);
        $_this = $this;
        return array_map( 
            function ($item) use ($_this) {
                return $_this->castToSingleFormValue($item);
            }, $value
        );
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
    public function castValueFromDb($value)
    {
        $value = Arr::wrap($value);
        $_this = $this;
        return array_map( 
            function ($item) use ($_this) {
                return $_this->castSingleValueFromDb($item);
            }, $value
        );
    }

    /**
     * @inheritDoc
     */
    public function formValue(BaseModel $model)
    {
        $value = $model->exists ? $model->{$this->machineName()} : null;
        return $this->castToFormValue($value);
    }

    /**
     * @inheritDoc
     */
    public function toFormElement($model): FormElement
    {
        $baseOptions = $model->formLayout()->getField($this->machineName())->options->values();
        $widget = $model->formLayout()->getField($this->machineName())->widget;
        $baseOptions['label'] = $this->name();
        $baseOptions['showLabel'] = false;

        $values = $this->formValue($model);
        $fields = [];
        if ($values) {
            foreach ($values as $index => $value) {
                $baseOptions['default'] = $value;
                $fields[] = $this->toSingleFormElement($widget, $baseOptions, $index);
            }
        } else {
            $baseOptions['default'] = $this->defaultValue();
            $fields[] = $this->toSingleFormElement($widget, $baseOptions, 0);
        }
        $options = [
            'helper' => $this->field->helper,
            'label' => $this->name(),
        ];
        return new FieldGroup($this->machineName(), $options, $fields, $this->cardinality());
    }

    /**
     * Turns a single field into a form element
     * 
     * @param string $widget
     * @param array  $baseOptions
     * @param int    $index
     * 
     * @return FormElement
     */
    protected function toSingleFormElement(string $widget, array $baseOptions, int $index)
    {
        $baseOptions['id'] = $this->machineName().$index;
        $baseOptions['htmlName'] = $this->machineName().'['.$index.']';
        $baseOptions['index'] = 0;
        $options = array_merge($baseOptions, $this->formFieldOptions($index));
        $field = new $widget($this->machineName(), $options);
        return $field;
    }

    /**
     * @inheritDoc
     */
    public function toFilterFormElement(): FormElement
    {
        $widget = \FormField::getRegisteredField($this->filterWidget());
        $options = $this->formFieldOptions();
        $options['htmlName'] = 'filters['.$this->machineName().']';
        $options['required'] = false;
        $field = new $widget($this->machineName(), $options);
        return $field;
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

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value, BaseModel $entity)
    {
        $fieldId = $this->field->id;
        $_this = $this;
        $query->leftJoin('bundle_field_values', function ($join) use ($entity, $fieldId) {
            $join->on('bundle_field_values.entity_id', '=', $entity->getTable() . '.id')
                ->where('bundle_field_values.entity_type', '=', get_class($entity))
                ->where('bundle_field_values.field_id', '=', $fieldId);
        });
        $this->singleFilterQueryModifier($query, $value, $entity);
    }
}