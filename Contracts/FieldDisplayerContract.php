<?php 

namespace Pingu\Field\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Pingu\Entity\Support\Entity;
use Pingu\Field\Support\DisplayOptions;

interface FieldDisplayerContract extends Arrayable
{       
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

    public function systemView(): string;

    public function getFieldValues(Entity $entity): array;
}