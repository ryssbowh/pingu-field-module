<?php

namespace Pingu\Field\Contracts;

use Pingu\Field\Support\FieldRevision;
use Pingu\Field\Support\RevisionRepository;

interface HasRevisionsContract
{
    public function revisionRepository(): RevisionRepository;
}