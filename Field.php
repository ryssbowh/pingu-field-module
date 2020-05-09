<?php 

namespace Pingu\Field;

use Illuminate\Support\Arr;
use Pingu\Core\Exceptions\ClassException;
use Pingu\Entity\Support\Entity;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Contracts\FieldRepositoryContract;
use Pingu\Field\Exceptions\BundleFieldException;

class Field
{
    /**
     * List of registered bundle fields
     * @var array
     */
    protected $bundleFields = [];

    /**
     * Registers a type of bundle field
     * 
     * @param BundleFieldContract $field
     * 
     * @throws ClassException
     */
    public function registerBundleField(BundleFieldContract $field)
    {
        $name = $field::uniqueName();
        if (isset($this->bundleFields[$name])) {
            throw BundleFieldException::registered($name, $field, $this->bundleFields[$name]);
        }
        $this->bundleFields[$name] = get_class($field);
    }

    /**
     * Registers multiple bundle fields
     * 
     * @param array|string $fieldClasses
     */
    public function registerBundleFields($fieldClasses)
    {
        $fieldClasses = Arr::wrap($fieldClasses);
        foreach ($fieldClasses as $fieldClass) {
            $this->registerBundleField(new $fieldClass);
        }
    }

    /**
     * Get all registered bundle fields
     * 
     * @return array
     */
    public function getRegisteredBundleFields(): array
    {
        return $this->bundleFields;
    }

    /**
     * Get a registered bundle field class name
     * 
     * @param string $name
     * 
     * @throws BundleFieldException
     *
     * @return string
     */
    public function getRegisteredBundleField(string $name): string
    {
        if (!isset($this->bundleFields[$name])) {
            throw BundleFieldException::notRegistered($name);
        }
        return $this->bundleFields[$name];
    }

    /**
     * Gets a field repository instance for an object.
     * Will register it in the IOC
     * 
     * @param object|string $object
     * @param Closure       $callback
     * 
     * @return FieldRepository
     */
    public function getFieldRepository($object, $callback): FieldRepositoryContract
    {
        $key = config('field.cache-keys.repositories').'.'.object_to_class($object);
        if (!app()->bound($key)) {
            app()->instance($key, $callback());
        }
        return app()[$key];
    }

    /**
     * Clears cache for an object
     * 
     * @param object|string $object
     */
    public function forgetFieldCache($object)
    {
        $object = object_to_class($object);
        $key = config('field.cache-keys.fields').'.'.$object;
        \ArrayCache::forget($key);
    }

    /**
     * Clears cache for all objects
     */
    public function forgetAllFieldCache()
    {
        \ArrayCache::forget(config('field.cache-keys.fields'));
    }

    /**
     * Get a revision related cache for an entity
     * 
     * @param string   $key
     * @param Entity   $entity
     * @param callable $callback
     * 
     * @return mixed   
     */
    public function getBundleValuesCache(Entity $entity, $callback)
    {   
        if (config('field.useCache', false)) {
            $key = config('field.cache-keys.values').'.'.get_class($entity).'.'.$entity->getKey();
            return \ArrayCache::rememberForever($key, $callback);
        }
        return $callback();
    }

    /**
     * Clears revision related cache for an entity
     * 
     * @param Entity $entity
     */
    public function forgetBundleValuesCache(Entity $entity)
    {
        $key = config('field.cache-keys.values').'.'.get_class($entity).'.'.$entity->getKey();
        \ArrayCache::forget($key);
    }

    /**
     * Load or save an entity revision cache
     * 
     * @param Entity $entity
     * @param callable $callback
     * 
     * @return mixed
     */
    public function getRevisionCache(Entity $entity, $callback)
    {
        if (config('field.useCache', false)) {
            $key = config('field.cache-keys.revisions').'.'.get_class($entity).'.'.$entity->getKey();
            return \ArrayCache::rememberForever($key, $callback);
        }
        return $callback();
    }
}