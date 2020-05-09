<?php 

namespace Pingu\Field;

use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Contracts\FieldContextRepositoryContract;
use Pingu\Field\Support\FieldContext\BaseFieldContextRepository;

class FieldContext
{   
    /**
     * Context repositories
     * @var array
     */
    protected $repositories = [];

    /**
     * Get context repository for a model
     * 
     * @param string|BaseModel $model
     * 
     * @return FieldContextRepositoryContract
     */
    public function getRepository($model): FieldContextRepositoryContract
    {
        $class = object_to_class($model);
        if (!isset($this->repositories[$class])) {
            $this->repositories[$class] = $class::$contextRepository ?? new BaseFieldContextRepository;
        }
        if (is_string($repo = $this->repositories[$class])) {
            $this->repositories[$class] = new $repo;
        }
        return $this->repositories[$class];
    }

    /**
     * Register context repository for a model
     * 
     * @param string|BaseModel $model
     * @param strint|FieldContextRepositoryContract $repository
     */
    public function registerRepository($model, $repository)
    {
        $this->repositories[object_to_class($model)] = object_to_class($repository);
    }
}