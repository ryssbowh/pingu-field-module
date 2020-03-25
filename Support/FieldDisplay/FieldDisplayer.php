<?php

namespace Pingu\Field\Support\FieldDisplay;

use Pingu\Field\Contracts\FieldDisplayerContract;

abstract class FieldDisplayer implements FieldDisplayerContract
{
    protected $options;

    /**
     * Constructor
     * 
     * @param array|null $options
     */
    public function __construct(?array $options = null)
    {
        if ($this->hasOptions()) {
            $class = $this::optionsClass();
            $this->options = new $class($options);
        }
    }

    /**
     * @ingeritDoc
     */
    public function options(): ?DisplayOptions
    {
        return $this->options;
    }

    /**
     * @ingeritDoc
     */
    public function toArray()
    {
        return [
            'hasOptions' => $this::hasOptions(),
            'machineName' => $this::machineName(),
            'friendlyName' => $this::friendlyName(),
            'options' => $this::hasOptions() ? $this->options->toArray() : []
        ];
    }
}