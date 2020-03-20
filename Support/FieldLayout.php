<?php 

namespace Pingu\Field\Support;

use Illuminate\Support\Collection;
use Pingu\Field\Contracts\DefinesFields;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Entities\FormLayout;
use Pingu\Field\Entities\FormLayoutGroup;

class FieldLayout
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
    protected $layout;

    /**
     * Default group name
     * 
     * @var string
     */
    protected $defaultGroup = 'Default';

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
     * Loads layout from db
     * 
     * @return FieldLayout
     */
    public function load($force = false): FieldLayout
    {
        if ($this->loaded and !$force) {
            return $this;
        }
        $this->layout = $this->resolveCache();
        if ($this->layout->isEmpty()) {
            $this->create();
            $this->layout = $this->resolveCache();
        }
        $this->loaded = true;
        return $this;
    }

    protected function resolveCache()
    {
        $object = $this->getObjectAttribute();
        return \Field::getFormLayoutCache($object, function () use ($object) {
            return FormLayoutGroup::where('object', $object)
                ->orderBy('weight')
                ->get();
        });
    }

    /**
     * Get actual layout
     * 
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->layout;
    }

    /**
     * Does a group has a field
     * 
     * @param string $group
     * @param string $field
     * 
     * @return bool
     */
    public function groupHasField(string $group, string $field): bool
    {
        if (!$this->layout->has($group)) {
            return false;
        }
        return $this->layout->get($group)->hasField($field);
    }

    /**
     * Create an empty group
     * 
     * @param string $name
     * 
     * @return FormLayoutGroup
     */
    public function createGroup(string $name): FormLayoutGroup
    {
        $group = FormLayoutGroup::create([
            'name' => $name,
            'object' => $this->getObjectAttribute()
        ]);
        $this->layout->put($name, $group);
        return $group;
    }

    /**
     * Get a field by its name
     * 
     * @param  string $name
     * @return ?FormLayout
     */
    public function getField(string $name): ?FormLayout
    {
        foreach ($this->layout as $group) {
            if ($group->hasField($name)) {
                return $group->getField($name);
            }
        }
        return null;
    }

    /**
     * Does a group exists
     * 
     * @param string $name
     * 
     * @return boolean
     */
    public function hasGroup(string $name): bool
    {
        return !is_null($this->layout->where('name', $name)->first());
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
        foreach ($this->layout as $group) {
            if ($group->hasField($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Group getter
     * 
     * @param  string $name
     * @return FormLayoutGroup
     */
    public function getGroup(string $name): FormLayoutGroup
    {
        return $this->layout->where('name', $name)->first();
    }

    /**
     * Creates form layout in database, will not recreate existing layout
     */
    public function create()
    {
        $group = $this->getDefaultGroup();
        foreach ($this->getFields() as $field) {
            $this->createForField($field, $group);
        }
    }

    /**
     * Create a FormLayout model for a field and a group
     * 
     * @param FieldContract   $field
     * @param FormLayoutGroup $group
     * 
     * @return FormLayout
     */
    public function createForField(FieldContract $field, ?FormLayoutGroup $group = null): ?FormLayout
    {
        if ($this->hasField($field->machineName())) {
            return null;
        }
        if (is_null($group)) {
            $group = $this->getDefaultGroup();
        }
        $layout = new FormLayout;
        $widget = \FormField::defaultWidget(get_class($field));
        $layout->fill([
            'field' => $field->machineName(),
            'object' => $this->getObjectAttribute(),
            'widget' => $widget,
            'options' => \FormField::getRegisteredField($widget)::defaultOptions()
        ]);
        $layout->group()->associate($group);
        $layout->save();
        $group->addField($layout);
        return $layout;
    }

    /**
     * Does this layout have any field
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        foreach ($this->layout as $group) {
            if (!$group->layout->isEmpty()) {
                return false;
            }
        }
        return true;
    }

    public function toFormGroups()
    {
        $out = [];
        foreach ($this->layout as $group) {
            $out[$group->name] = $group->layout->pluck('field')->toArray();
        }
        return $out;
    }

    /**
     * @return Collection
     */
    protected function getFields(): Collection
    {
        return $this->object->fields()->get();
    }

    /**
     * Get (or create) default group
     * 
     * @return FormLayoutGroup
     */
    protected function getDefaultGroup(): FormLayoutGroup
    {
        if (!$this->hasGroup($this->defaultGroup)) {
            return $this->createGroup($this->defaultGroup);
        } else {
            return $this->getGroup($this->defaultGroup);
        }
    }

    /**
     * Which string is to be saved in the 'object' field of FormLayoutGroup and FormLayout
     * 
     * @return string
     */
    protected function getObjectAttribute()
    {
        return get_class($this->object);
    }
}