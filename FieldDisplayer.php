<?php

namespace Pingu\Field;

use Illuminate\Support\Arr;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Field\Exceptions\DisplayerException;
use Pingu\Field\Support\FieldDisplay\FieldDisplay as FieldDisplayHandler;

class FieldDisplayer
{
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
     * Registers field displayers
     * 
     * @param string $field
     * 
     * @param string|array $displayers
     */
    public function register($displayer)
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
    public function append(string $field, $displayer)
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
    public function prepend(string $field, $displayer)
    {
        $displayers = Arr::wrap($displayer);
        $displayers = array_map(function ($displayer) {
            return $displayer::machineName();
        }, $displayers);
        $this->fieldDisplayers[$field] = array_merge($displayers, $this->fieldDisplayers[$field] ?? []);
    }

    public function getForField($field, $asClass = false)
    {
        $field = object_to_class($field);
        if (!($this->fieldDisplayers[$field] ?? [])) {
            throw DisplayerException::noDisplayersForField($field);
        }
        $_this = $this;
        return array_map(function ($displayer) use ($asClass, $_this) {
            return $asClass ? $_this->getDisplayer($displayer) : $displayer;
        }, $this->fieldDisplayers[$field]);
    }

    public function getListForField($field)
    {
        $out = [];
        foreach ($this->getForField($field, true) as $displayer) {
            $out[$displayer::machineName()] = $displayer::friendlyName();
        }
        return $out;
    }

    public function getDisplayer(string $name)
    {
        if (!$this->isRegistered($name)) {
            throw DisplayerException::notRegistered($name);
        }
        return $this->displayers[$name];
    }

    public function isRegistered(string $name)
    {
        return isset($this->displayers[$name]) and !empty($this->displayers[$name]);
    }

    public function defaultDisplayer($field, bool $asClass = false)
    {
        $field = object_to_class($field);
        return $this->getForField($field, $asClass)[0];
    }
}