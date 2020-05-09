<?php

namespace Pingu\Field\Traits;

use Pingu\Field\Contracts\FieldRepositoryContract;

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
    protected function fieldRepositoryInstance(): FieldRepositoryContract
    {
        $fieldsClass = 'Fields\\'.class_basename(get_class($this)).'Fields';
        $class = \Str::replaceLast(class_basename(get_class($this)), $fieldsClass, get_class($this));
        return new $class($this);
    }

    /**
     * Fills a model with values and saves it, according to its fields.
     * Will also save the relationships if the fields define some
     * 
     * @param  array        $values
     * @param  bool|boolean $casted
     * @return bool
     */
    public function saveFields(array $values): bool
    {
        if ($this->exists) {
            /**
             * If the model exists we can save all the attributes and relations at once.
             */
            $changed = $this->fillAllFields($values);
            $saved = $this->save();
        } else {
            /**
             * If the model doesn't exist we can't save the syncable relations (HasMany, BelongsToMany)
             * until the model has an id. So we'll save the attributes and simple relations (HasOne, BelongsTo)
             * first and then sync the syncable relations.
             */
            $this->fillAllFields($values, true);
            $saved = $this->save();
            $changed = $this->syncSyncableFields($values);
        }

        return ($saved or $changed);
    }

    /**
     * Syncs all values for fields that define syncable relations (HasMany, BelongsToMany)
     * 
     * @param array $values
     * 
     * @return bool Has a syncable relation changed
     */
    protected function syncSyncableFields(array $values): bool
    {
        $changed = false;
        foreach ($values as $name => $value) {
            $field = $this->fieldRepository()->get($name);
            if ($field->definesSyncableRelation()) {
                $syncedChanged = $field->saveOnModel($this, $value);
                $changed = ($changed or $syncChanged);
            }
        }
        return $changed;
    }

    /**
     * Fill attributes, simple and syncable relations.
     * 
     * @param array  $values
     * 
     * @return bool Has a syncable relation changed
     */
    protected function fillAllFields(array $values, bool $skipSyncable = false)
    {
        $changed = false;
        foreach ($values as $name => $value) {
            $field = $this->fieldRepository()->get($name);
            $definesSyncable = $field->definesSyncableRelation();
            if ($skipSyncable and $definesSyncable) {
                continue;
            }
            $syncedChanged = $field->saveOnModel($this, $value);
            if ($definesSyncable) {
                $changed = ($changed or $syncChanged);
            }
        }
        return $changed;
    }
}