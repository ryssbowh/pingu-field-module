<?php 

namespace Pingu\Field;

use Illuminate\Support\Arr;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Field\Exceptions\DisplayerException;
use Pingu\Field\Support\FieldDisplay\FieldDisplay as FieldDisplayHandler;

class FieldDisplay
{
    /**
     * List of registered field displays
     * @var array
     */
    protected $fieldDisplays = [];

    /**
     * Field -> Displayer mapping
     * @var array
     */
    protected $fieldDisplayers = [];

    /**
     * List of registered displayers
     * @var array
     */
    protected $displayers = [];

    /**
     * Load or save an object form layout
     * 
     * @param string   $object
     * @param callable $callback
     */
    public function getDisplayCache(string $object, $callback)
    {
        if (config('field.useCache', false)) {
            $key = 'field.display.'.$object;
            return \ArrayCache::rememberForever($key, $callback);
        }
        return $callback();
    }

    /**
     * Registers a form layout for a class
     * 
     * @param string      $slug   class name
     * @param FieldLayout $layout
     */
    public function registerDisplay(string $slug, FieldDisplayHandler $display)
    {
        $this->fieldDisplays[$slug] = $display;
    }

    /**
     * Get a FormLayout class for a Bundle
     * 
     * @param BundleContract $bundle
     * 
     * @return FieldDisplay
     */
    public function getBundleDisplay(BundleContract $bundle): FieldDisplayHandler
    {
        $object = $bundle->bundleName();
        return isset($this->fieldDisplays[$object]) ? $this->fieldDisplays[$object]->load() : null;
    }

    /**
     * Forget the form layout cache for an object
     * 
     * @param string $object
     */
    public function forgetDisplayCache(string $object)
    {
        \ArrayCache::forget('field.display.'.$object);
    }

    /**
     * Registers field displayers
     * 
     * @param string $field
     * 
     * @param string|array $displayers
     */
    public function registerDisplayers($displayer)
    {
        $displayers = Arr::wrap($displayer);
        foreach ($displayers as $displayer) {
            $this->displayers[$displayer::machineName()] = $displayer;
        }
    }

    /**
     * Appends to field displayers mapping
     * 
     * @param string $field
     * 
     * @param string|array $displayers
     */
    public function appendFieldDisplayer(string $field, $displayer)
    {
        $displayers = Arr::wrap($displayer);
        $displayers = array_map(function ($displayer) {
            return $displayer::machineName();
        }, $displayers);
        $this->fieldDisplayers[$field] = array_merge($this->fieldDisplayers[$field] ?? [], $displayers);
    }

    /**
     * Prepends to field displayers mapping
     * 
     * @param string $field
     * 
     * @param string|array $displayers
     */
    public function prependFieldDisplayer(string $field, $displayer)
    {
        $displayers = Arr::wrap($displayer);
        $displayers = array_map(function ($displayer) {
            return $displayer::machineName();
        }, $displayers);
        $this->fieldDisplayers[$field] = array_merge($displayers, $this->fieldDisplayers[$field] ?? []);
    }

    public function getDisplayersForField($field, $asClass = false)
    {
        $field = object_to_class($field);
        if (!($this->fieldDisplayers[$field] ?? [])) {
            throw DisplayerException::noDisplayersForField($field);
        }
        $_this = $this;
        return array_map(function ($displayer) use ($asClass, $_this) {
            return $asClass ? $_this->getRegisteredDisplayer($displayer) : $displayer;
        }, $this->fieldDisplayers[$field]);
    }

    public function getDisplayersListForField($field)
    {
        $out = [];
        foreach ($this->getDisplayersForField($field, true) as $displayer) {
            $out[$displayer::machineName()] = $displayer::friendlyName();
        }
        return $out;
    }

    public function getRegisteredDisplayer(string $name)
    {
        if (!$this->isDisplayerRegistered($name)) {
            throw DisplayerException::notRegistered($name);
        }
        return $this->displayers[$name];
    }

    public function isDisplayerRegistered(string $name)
    {
        return isset($this->displayers[$name]) and !empty($this->displayers[$name]);
    }

    public function defaultDisplayerForField($field, bool $asClass = false)
    {
        $field = object_to_class($field);
        return $this->getDisplayersForField($field, $asClass)[0];
    }
}