<?php 

namespace Pingu\Field\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Pingu\Field\Support\DisplayOptions;

interface FieldDisplayerContract extends Arrayable
{
    /**
     * Class used to handle options
     * 
     * @return string
     */
    public static function optionsClass(): string;
        
    /**
     * Machine name for this displayer
     * 
     * @return string
     */
    public static function machineName(): string;

    /**
     * Friendly name for this displayer
     * 
     * @return string
     */
    public static function friendlyName(): string;

    /**
     * Does this displayer define options
     * 
     * @return boolean
     */
    public static function hasOptions(): bool;

    /**
     * Get the options instance
     * 
     * @return DisplayOptions
     */
    public function options(): ?DisplayOptions;
}