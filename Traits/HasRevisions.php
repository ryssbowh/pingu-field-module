<?php 

namespace Pingu\Field\Traits;

use Pingu\Field\Support\FieldRevision;
use Pingu\Field\Support\RevisionRepository;

trait HasRevisions
{
    protected $revisionRepository;

    public static function bootHasRevisions()
    {
        static::saved(
            function ($entity) {
                $entity->revisionRepository()->createRevision();
            }
        );
    }

    public function initializeHasRevisions()
    {
        $this->revisionRepository = new RevisionRepository($this);
    }

    public function revisionRepository(): RevisionRepository
    {
        return $this->revisionRepository->load();
    }
}