<?php

namespace Pingu\Field\Support;

use Pingu\Entity\Entities\DisplayField;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Contracts\FieldDisplayerContract;
use Pingu\Field\Support\DisplayOptions;

abstract class FieldDisplayerWithOptions extends FieldDisplayer
{   
    /**
     * @var ?DisplayOptions
     */
    protected $options;

    /**
     * Constructor
     * 
     * @param array|null $options
     */
    public function __construct(DisplayField $field)
    {
        parent::__construct($field);
        $class = $this::optionsClass();
        $this->options = new $class($this);
        $this->options->setvalues($field->options);
    }

    /**
     * Class used to handle options
     * 
     * @return string
     */
    public abstract static function optionsClass(): string;

    /**
     * @inheritDoc
     */
    public static function hasOptions(): bool
    {
        return true;
    }

    /**
     * Option getter
     * 
     * @param string $name
     * 
     * @return mixed
     */
    public function option($name)
    {
        return $this->options->get($name);
    }

    /**
     * Options getter
     * 
     * @return DisplayOptions
     */
    public function options(): DisplayOptions
    {
        return $this->options;
    }

    /**
     * Set options for this displayer
     * 
     * @param array $options
     *
     * @return FieldDisplayerWithOptions
     */
    public function setOptions(array $options): FieldDisplayerWithOptions
    {
        $this->options->setValues($options);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['options'] = $this->options->toArray();
        return $array;
    }
}