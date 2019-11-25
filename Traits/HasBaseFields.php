<?php

namespace Pingu\Field\Traits;

use Illuminate\Support\Str;
use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Contracts\FieldsValidator;
use Pingu\Field\Entities\BundleField;

trait HasBaseFields
{
    use HasFields;

    /**
     * Gets the field repository for this entity.
     * Will look for a class called {EntityClass}Fields situated in
     * the Fields folder of the Entities Folder
     * 
     * @return FieldRepository
     */
    protected function getFieldRepository(): FieldRepository
    {
        $fieldsClass = 'Fields\\'.class_basename(get_class($this)).'Fields';
        $class = Str::replaceLast(class_basename(get_class($this)), $fieldsClass, get_class($this));
        return new $class($this);
    }

    /**
     * Gets the field validator for this entity.
     * Will look for a class called {EntityClass}Validator situated in
     * the Validators folder of the Entities Folder
     * 
     * @return FieldsValidator
     */
    protected function getFieldsValidator(): FieldsValidator
    {
        $validatorClass = 'Validators\\'.class_basename(get_class($this)).'Validator';
        $class = Str::replaceLast(class_basename(get_class($this)), $validatorClass, get_class($this));
        return new $class($this);
    }

    /**
     * Gets the field repository for this entity by loading it from the Field Facade
     * 
     * @return FieldRepository
     */
    public function fields(): FieldRepository
    {
        $_this = $this;
        return \Field::getFieldRepository(
            $this,
            function () use ($_this) {
                return $_this->getFieldRepository();
            }
        );
    }

    /**
     * Gets the field validator for this entity by loading it from the Field Facade
     * 
     * @return FieldsValidator
     */
    public function validator(): FieldsValidator
    {
        $_this = $this;
        return \Field::getFieldsValidator(
            $this,
            function () use ($_this) {
                return $_this->getFieldsValidator();
            }
        );
    }
    
    /**
     * Syncs all values for fields that define 'multiple' relations
     * 
     * @param  array  $relations
     * @return bool
     */
    protected function syncMultipleRelations(array $relations): bool
    {
        $changes = false;
        foreach ($relations as $name => $value) {
            if ($this->$name()->sync($value)) {
                $changes = true;
            }
            $this->load($name);
        }
        return $changes;
    }

    /**
     * Fill all values for fields that define 'single' relations
     * 
     * @param array  $relations
     * 
     * @return bool           
     */
    protected function fillSingleRelations(array $relations): bool
    {
        $changes = false;
        foreach ($relations as $name => $value) {
            $oldValue = $this->$name ? $this->$name->getKey() : null;

            if ($value) {
                $this->$name()->associate($value);
            } else {
                $this->$name()->dissociate();
            }

            $value = $value ? $value->getKey() : null;
            
            if ($oldValue != $value) {
                $changes = true;
            }
        }
        return $changes;
    }

    /**
     * Fill a model with values and saves it.
     * Will fill the model's attribute according to the field
     * 
     * @param  array        $values
     * @param  bool|boolean $casted
     * @return bool
     */
    public function saveWithRelations(array $values, bool $casted = true): bool
    {
        if (!$casted) {
            $values = $this->validator()->castValues($values);
        }
        $fieldTypes = $this->sortFieldTypes($values);
        // dump($fieldTypes);
        $this->fill($fieldTypes['attributes'] ?? []);
        $this->fillSingleRelations($fieldTypes['relations']['single'] ?? []);

        if (!$this->save()) {
            return false;
        }
        try{
            $changesRelation = $this->syncMultipleRelations($fieldTypes['relations']['multiple'] ?? []);
        }
        catch(\Exception $e){
            return false;
        }

        return true;
    }

    /**
     * Sort field values, will return an array of this type :
     * [
     *     'attributes' => ['attribute1' => 'value1', 'attribute2' => 'value2'],
     *     'relations' => [
     *         'single' => ['attribute3' => 'value3'],
     *         'multiple' => ['attribute4' => ['value4', 'value5']]
     *     ]
     * ]
     * 
     * @param  array  $values
     * @return array
     */
    protected function sortFieldTypes(array $values): array
    {
        $types = [];
        $fields = $this->fields();
        foreach ($values as $name => $value) {
            if (!$fields->has($name)) {
                continue;
            }
            $field = $fields->get($name);
            $relation = $field->definesRelation();
            if ($relation === false) {
                $types['attributes'][$name] = $value;
            } else {
                $types['relations'][$relation][$name] = $value;
            }
        }
        return $types;
    }
}