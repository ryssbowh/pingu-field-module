<?php

namespace Pingu\Field\Support\FieldDisplay;

use Illuminate\Support\Collection;
use Pingu\Field\Contracts\DefinesFields;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Entities\DisplayField;

class FieldDisplay
{
    /**
     * @var object
     */
    protected $object;

    /**
     * @var boolean
     */
    protected $loaded = false;

    /**
     * @var Collection
     */
    protected $display;

    /**
     * Constructor
     * 
     * @param HasFields $object
     */
    public function __construct(DefinesFields $object)
    {
        $this->object = $object;
    }

    /**
     * Loads displays from db
     * 
     * @return FieldDisplay
     */
    public function load($force = false): FieldDisplay
    {
        if ($this->loaded and !$force) {
            return $this;
        }
        $this->display = $this->resolveCache();
        if ($this->display->isEmpty()) {
            $this->display = collect();
            $this->create();
        }
        $this->loaded = true;
        return $this;
    }

    /**
     * Resolve display cache
     * 
     * @return Collection
     */
    protected function resolveCache()
    {
        $_this = $this;
        return \FieldDisplay::getDisplayCache($this->getObjectAttribute(), function () use ($_this) {
            return $_this->loadDisplay();
        });
    }

    /**
     * Get displays from db
     * 
     * @return Collection
     */
    protected function loadDisplay()
    {
        return DisplayField::where('object', $this->getObjectAttribute())
            ->orderBy('weight')
            ->get()
            ->keyBy(function ($item) {
                return $item->field;
            });
    }

    /**
     * Get actual display
     * 
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->display;
    }

    /**
     * Get a field by its name
     * 
     * @param  string $name
     * @return ?FormLayout
     */
    public function getField(string $name): ?FormLayout
    {
        return $this->display->get($name, null);
    }

    /**
     * Does a field exists
     * 
     * @param string $name
     * 
     * @return boolean
     */
    public function hasField(string $name): bool
    {
        return $this->display->has($name);
    }

    /**
     * Creates field displays in database, will not recreate existing displays
     */
    public function create()
    {
        foreach ($this->getFields() as $field) {
            $this->createForField($field);
        }
    }

    /**
     * Create a DisplayField model for a field
     * 
     * @param FieldContract   $field
     * 
     * @return bool
     */
    public function createForField(FieldContract $field): bool
    {
        if ($this->hasField($field->machineName())) {
            return false;
        }
        $display = new DisplayField;
        $displayer = $field::defaultDisplayer(true);
        $display->fill([
            'field' => $field->machineName(),
            'object' => $this->getObjectAttribute(),
            'displayer' => $displayer::machineName(),
            'options' => $displayer::hasOptions() ? $displayer->options()->values() : [],
            'label' => 1
        ]);
        $display->save();
        $this->display->put($field->machineName(), $display);
        return true;
    }

    /**
     * Delete display for one field
     * 
     * @param FieldContract $field
     */
    public function deleteForField(FieldContract $field)
    {
        if ($this->hasField($field->machineName())) {
            $this->display[$field->machineName()]->delete();
            $this->display->forget($field->machineName());
        }
    }

    /**
     * Delete all fields display
     */
    public function delete()
    {
        foreach ($this->display as $display) {
            $display->delete();
        }
    }

    /**
     * Does this layout have any field
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->display->isEmpty();
    }

    /**
     * Get the fields defined by the associated object
     * 
     * @return Collection
     */
    protected function getFields(): Collection
    {
        return $this->object->fields()->get();
    }

    /**
     * Which string is to be saved in the 'object' field of DisplayField
     * 
     * @return string
     */
    protected function getObjectAttribute()
    {
        return get_class($this->object);
    }
}