<?php 

namespace Pingu\Field\Displayers;

use Pingu\Entity\Support\Entity;
use Pingu\Field\Support\FieldDisplayer;

class TitleTextDisplayer extends FieldDisplayer
{
    /**
     * @ineritDoc
     */
    public static function friendlyName(): string
    {
        return 'Title';
    }

    /**
     * @ineritDoc
     */
    public static function machineName(): string
    {
        return 'text-title';
    }

    public function systemView(): string
    {
        return 'field@fields.text-title';
    }

    public function getFieldValue($value)
    {
        return $value;
    }
}