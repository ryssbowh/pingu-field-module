<?php 

namespace Pingu\Field\Traits;

use Pingu\Field\Support\FieldRevision;
use Pingu\Field\Support\RevisionRepository;

trait HasRevisions
{
    /**
     * @var RevisionRepository
     */
    protected $revisionRepository;

    /**
     * Boots trait. creates revision when model is created
     */
    public static function bootHasRevisions()
    {
        static::saved(
            function ($entity) {
                $entity->revisionRepository()->createRevision();
            }
        );
    }

    /**
     * Initialize trait.
     */
    public function initializeHasRevisions()
    {
        $this->revisionRepository = new RevisionRepository($this);
    }

    /**
     * Revision repository getter
     * 
     * @return RevisionRepository
     */
    public function revisionRepository(): RevisionRepository
    {
        return $this->revisionRepository->load();
    }
}