<?php 

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Entity\Support\Entity;
use Pingu\Field\Context\UpdateBundleFieldContext;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Entities\BundleFieldValue;
use Pingu\Field\Exceptions\BundleFieldException;
use Pingu\Field\Support\BundleFieldForms;
use Pingu\Field\Traits\HasDisplayers;
use Pingu\Field\Traits\HasFilterWidgets;
use Pingu\Field\Traits\HasWidgets;
use Pingu\Forms\Contracts\FormRepositoryContract;
use Pingu\Forms\Support\FieldGroup;
use Pingu\Forms\Support\FormElement;

abstract class BaseBundleField extends BaseModel implements BundleFieldContract
{
    use HasWidgets, HasFilterWidgets, HasDisplayers;

    protected $with = ['field'];

    public static $routeContexts = [UpdateBundleFieldContext::class];

    protected $genericFields = ['machineName', 'bundle', 'helper', 'name', 'cardinality', 'editable', 'deletable'];

    public static function boot()
    {
        parent::boot();

        static::registered(function ($field) {
            \Field::registerBundleField($field);
            $field::registerWidgets();
            $field::registerFilterWidgets();
            $field::registerDisplayers();
        });

        static::updated(function ($field) {
            $field->field->save();
        });
    }

    /**
     * Field mutator. Instanciate an empty field relation
     * 
     * @return BundleField
     */
    public function getFieldAttribute()
    {
        if (!$field = $this->getRelationValue('field')) {
            $field = new BundleField;
            $this->setRelation('field', $field);
        }
        return $field;
    }

    /**
     * @inheritDoc
     */
    public function setAttribute($key, $value) {
        if (in_array($key, $this->field->getFillable())) {
            return $this->field->setAttribute($key, $value);
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($key)
    {
        if (in_array($key, $this->genericFields)) {
            return $this->field->getAttribute($key);
        }
        return parent::getAttribute($key);
    }

    /**
     * @inheritDoc
     */
    public function wasChanged($attributes = null)
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();
        return (parent::wasChanged($attributes) or $this->field->wasChanged($attributes));
    }

    /**
     * @inheritDoc
     */
    public function isDirty($attributes = null)
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();
        return (parent::isDirty($attributes) or $this->field->isDirty($attributes));
    }

    /**
     * @inheritDoc
     */
    public function saveOnModel(BaseModel $model, $value): bool
    {
        $model->{$this->machineName} = $value;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function definesSyncableRelation(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function filterable(): bool
    {
        return true;
    }
    
    /**
     * Restrict deleting to force developers to delete the BundleField instead
     * 
     * @throws BundleFieldException
     */
    public function delete($force = false)
    {
        if ($force) {
            return parent::delete();
        }
        throw BundleFieldException::cantDelete();
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function machineName(): string
    {
        return $this->machineName;
    }

    /**
     * @inheritDoc
     */
    public function cardinality(): int
    {
        return $this->cardinality;
    }

    /**
     * Forms instance for this field
     * 
     * @return FormRepositoryContract
     */
    public static function forms(): FormRepositoryContract
    {
        return new BundleFieldForms;
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
    public function uncastValue($value)
    {
        $value = Arr::wrap($value);
        $_this = $this;
        return array_map( 
            function ($item) use ($_this) {
                return $_this->uncastSingleValue($item);
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
        $value = $model->exists ? $model->{$this->machineName()} : $this->defaultValue();
        return $this->castToFormValue($value);
    }

    /**
     * @inheritDoc
     */
    public function toFormElement($values): FormElement
    {
        $baseOptions = $this->bundle()->fieldLayout()->getField($this->machineName())->options->values();
        $widget = $this->bundle()->fieldLayout()->getField($this->machineName())->widget;
        $baseOptions['label'] = $this->name();
        $baseOptions['showLabel'] = false;

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
    public function filterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        $fieldId = $this->field->id;
        $_this = $this;
        $query->leftJoin('bundle_field_values', function ($join) use ($model, $fieldId) {
            $join->on('bundle_field_values.entity_id', '=', $model->getTable() . '.id')
                ->where('bundle_field_values.entity_type', '=', get_class($model))
                ->where('bundle_field_values.field_id', '=', $fieldId);
        });
        $this->singleFilterQueryModifier($query, $value, $model);
    }
}