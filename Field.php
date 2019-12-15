<?php 

namespace Pingu\Field;

use Illuminate\Http\Request;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Exceptions\ClassException;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Entity\Entities\Entity;
use Pingu\Entity\Support\Bundle;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Contracts\FieldsValidator;
use Pingu\Field\Contracts\HasFields;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Exceptions\BundleFieldException;
use Pingu\Field\Support\FieldLayout;
use Pingu\Forms\Contracts\Models\FormableContract;
use Pingu\Forms\Exceptions\ModelNotFormable;

class Field
{
    /**
     * List of registered bundle fields
     * @var array
     */
    protected $bundleFields = [];

    /**
     * List of registered form layouts
     * @var array
     */
    protected $formLayouts = [];

    /**
     * Registers a type of bundle field
     * 
     * @param string $field
     * 
     * @throws BundleFieldException
     * @throws ClassException
     */
    public function registerBundleField(string $fieldClass)
    {
        $impl = class_implements($fieldClass);
        if (!isset($impl[BundleFieldContract::class])) {
            throw ClassException::missingInterface($fieldClass, BundleFieldContract::class);
        }
        $name = $fieldClass::uniqueName();
        if (isset($this->bundleFields[$name])) {
            throw BundleFieldException::registered($name, $field, $this->bundleFields[$name]);
        }
        $this->bundleFields[$name] = $fieldClass;
        $fieldClass::registerWidgets();
    }

    /**
     * Registers multiple bundle fields
     * 
     * @param array $fieldClasses
     */
    public function registerBundleFields(array $fieldClasses)
    {
        foreach ($fieldClasses as $fieldClass) {
            $this->registerBundleField($fieldClass);
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
    public function getFieldRepository($object, $callback): FieldRepository
    {
        $key = 'field.repository.'.object_to_class($object);
        if (!app()->bound($key)) {
            app()->instance($key, $callback());
        }
        return app()[$key];
    }

    /**
     * Gets a field validator instance for an object.
     * Will register it in the IOC
     * 
     * @param object|string $model
     * @param Closure       $callback
     * 
     * @return FieldRepository
     */
    public function getFieldsValidator($object, $callback)
    {
        $key = 'field.validator.'.object_to_class($object);
        if (!app()->bound($key)) {
            app()->instance($key, $callback());
        }
        return app()[$key];
    }

    /**
     * Retrieves a cache content for a object and a key.
     * Uses ArrayCache so all cache related to this object can be cleared at once.
     * 
     * @param string        $key
     * @param object|string $object
     * @param Closure       $callback
     * 
     * @return array
     */
    public function getFieldsCache(string $key, $object, $callback)
    {   
        if (config('field.useCache', false)) {
            $key = 'field.fields.'.object_to_class($object).'.'.$key;
            return \ArrayCache::rememberForever($key, $callback($object));
        }
        return $callback($object);
    }

    /**
     * Clears cache for an object
     * 
     * @param object $object
     */
    public function forgetFieldCache($object)
    {
        $object = object_to_class($object);
        $key = 'field.fields.'.$object;
        \ArrayCache::forget($key);
    }

    /**
     * Clears cache for all objects
     */
    public function forgetAllFieldCache()
    {
        \ArrayCache::forget('field.fields');
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
            $key = 'field.values.'.get_class($entity).'.'.$entity->getKey();
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
        $key = 'field.values.'.get_class($entity).'.'.$entity->getKey();
        \ArrayCache::forget($key);
    }

    public function getRevisionCache(Entity $entity, $callback)
    {
        if (config('field.useCache', false)) {
            $key = 'field.revisions.'.get_class($entity).'.'.$entity->getKey();
            return \ArrayCache::rememberForever($key, $callback);
        }
        return $callback();
    }

    public function registerFormLayout(string $slug, FieldLayout $layout)
    {
        $this->formLayouts[$slug] = $layout;
    }

    public function getFormLayout(string $object)
    {
        return isset($this->formLayouts[$object]) ? $this->formLayouts[$object]->load() : null;
    }

    public function getFormLayoutCache(string $object, $callback)
    {
        if (config('field.useCache', false)) {
            $key = 'field.layout.'.$object;
            return \ArrayCache::rememberForever($key, $callback($object));
        }
        return $callback($object);
    }

    public function forgetFormLayoutCache(string $object)
    {
        \ArrayCache::forget('field.layout.'.$object);
    }
}