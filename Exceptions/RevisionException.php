<?php

namespace Pingu\Field\Exceptions;

use Pingu\Entity\Entities\Entity;
use Pingu\Field\Contracts\HasFields;

class RevisionException extends \Exception
{
    public static function doesNotExist(Entity $entity, int $id)
    {
        return new static(get_class($entity)." does not have a revision $id");
    }

    public static function cantDeleteCurrent($id, Entity $entity)
    {
        return new static("Can't delete current revision($id) for entity ".get_class($entity));
    }
}