<?php

namespace Pingu\Field\Exceptions;

use Pingu\Entity\Entities\Entity;

class FieldRepositoryException extends \Exception
{
    public static function bundleNotSet(Entity $entity)
    {
        return new static("bundle is not set for the entity ".get_class($entity).". Use setBundle(\$bundle) before accessing the entity fields");
    }
}