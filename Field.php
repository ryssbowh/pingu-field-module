<?php 

namespace Pingu\Field;

use Illuminate\Http\Request;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Exceptions\ClassException;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Entity\Support\Bundle;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Contracts\FieldsValidator;
use Pingu\Field\Contracts\HasFields;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Exceptions\BundleFieldException;
use Pingu\Forms\Contracts\Models\FormableContract;
use Pingu\Forms\Exceptions\ModelNotFormable;

class Field
{
    protected $bundleFields = [];
    protected $cacheKeys = [];

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
    }

    /**
     * Registers multiple bundle fields
     * 
     * @param array  $fieldClasses
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
     * Gets a field repository instance for a bundle.
     * Will register it in the IOC
     * 
     * @param Bundle $bundle
     * @param Closure $callback
     * 
     * @return FieldRepository
     */
    public function getBundleFieldRepository(BundleContract $bundle, $callback): FieldRepository
    {
        $key = 'field.bundle-field-repository.'.$bundle->bundleName();
        if (!app()->bound($key)) {
            app()->instance($key, $callback());
        }
        return app()[$key];
    }

    /**
     * Gets a field validator instance for a bundle.
     * Will register it in the IOC
     * 
     * @param BundleContract $bundle
     * @param Closure        $callback
     * 
     * @return FieldsValidator
     */
    public function getBundleFieldsValidator(BundleContract $bundle, $callback): FieldsValidator
    {
        $key = 'field.fields-validator.'.get_class($bundle);
        if (!app()->bound($key)) {
            app()->instance($key, $callback());
        }
        return app()[$key];
    }

    /**
     * Gets a field repository instance for a model.
     * Will register it in the IOC
     * 
     * @param BaseModel $model
     * @param Closure   $callback
     * 
     * @return FieldRepository
     */
    public function getModelFieldRepository(BaseModel $model, $callback): FieldRepository
    {
        $key = 'field.field-repository.'.get_class($model);
        if ($model->exists) {
            $key .= '.'.$model->getKey();
        }
        if (!app()->bound($key)) {
            app()->instance($key, $callback());
        }
        return app()[$key];
    }

    /**
     * Gets a field validator instance for a model.
     * Will register it in the IOC
     * 
     * @param BaseModel $model
     * @param Closure   $callback
     * 
     * @return FieldRepository
     */
    public function getModelFieldsValidator(BaseModel $model, $callback)
    {
        $key = 'field.fields-validator.'.get_class($model);
        if ($model->exists) {
            $key .= '.'.$model->getKey();
        }
        if (!app()->bound($key)) {
            app()->instance($key, $callback());
        }
        return app()[$key];
    }

    /**
     * Retrieves a cache content for a object and a key.
     * Uses ArrayCache so all cache related to this object can be cleared at once.
     * 
     * @param string  $key
     * @param object  $object
     * @param Closure $callback
     * 
     * @return array
     */
    public function getCache(string $key, HasFields $object, $callback)
    {   
        if (config('field.useCache', false)) {
            $key = 'field.fields.'.get_class($object).'.'.$key;
            return \ArrayCache::rememberForever($key, $callback);
        }
        return $callback();
    }

    /**
     * Clears cache for an object
     * 
     * @param object $object
     */
    public function clearCache($object)
    {
        if (is_object($object)) {
            $object = get_class($object);
        }
        $key = 'field.fields.'.$object;
        \ArrayCache::forget($key);
    }

    /**
     * Clears cache for all objects
     */
    public function clearAllCache()
    {
        \ArrayCache::forget('field.fields');
    }
}