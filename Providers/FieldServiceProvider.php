<?php

namespace Pingu\Field\Providers;

use Illuminate\Database\Eloquent\Factory;
use Pingu\Core\Support\ModuleServiceProvider;
use Pingu\Field\BaseFields\{Boolean, Datetime, Email, Integer, LongText, ManyModel, Model, Password, Text, _Float, _List};
use Pingu\Field\Displayers\{DefaultBooleanDisplayer, DefaultDateDisplayer, DefaultEmailDisplayer, DefaultTextDisplayer, TitleTextDisplayer, TrimmedTextDisplayer, DefaultFloatDisplayer, DefaultIntegerDisplayer, DefaultUrlDisplayer, DefaultModelDisplayer};
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Entities\{BundleField as BundleFieldModel, BundleFieldValue, FieldBoolean, FieldDatetime, FieldEmail, FieldEntity, FieldFloat, FieldInteger, FieldText, FieldTextLong, FieldUrl};
use Pingu\Field\Field;
use Pingu\Field\FieldDisplay;
use Pingu\Field\FieldDisplayer;
use Pingu\Field\Observers\BundleFieldObserver;
use Pingu\Field\Observers\BundleFieldValueObserver;
use Pingu\Field\Support\FieldDisplayer as FieldDisplayerSupport;
use Pingu\Field\Validation\BundleFieldRules;
use Pingu\User\Bundles\UserBundle;
use Pingu\User\Entities\User;

class FieldServiceProvider extends ModuleServiceProvider
{
    /**
     * List of base fields defined by this module
     * @var array
     */
    protected $baseFields = [
        _Float::class,
        _List::class,
        Boolean::class,
        Datetime::class,
        Email::class,
        Integer::class,
        LongText::class,
        ManyModel::class,
        Model::class,
        Password::class,
        Text::class
    ];

    /**
     * List of bundle fields defined by this module
     * @var array
     */
    protected $entities = [
        FieldBoolean::class,
        FieldDatetime::class,
        FieldEmail::class,
        FieldFloat::class,
        FieldInteger::class,
        FieldText::class,
        FieldTextLong::class,
        FieldUrl::class,
        FieldEntity::class,
        BundleField::class
    ];

    /**
     * List of field displayer
     * @var array
     */
    protected $fieldDisplayers = [
        TrimmedTextDisplayer::class,
        DefaultTextDisplayer::class,
        TitleTextDisplayer::class,
        DefaultDateDisplayer::class,
        DefaultBooleanDisplayer::class,
        DefaultEmailDisplayer::class,
        DefaultFloatDisplayer::class,
        DefaultIntegerDisplayer::class,
        DefaultUrlDisplayer::class,
        DefaultModelDisplayer::class
    ];

    /**
     * Boot the application events.
     */
    public function boot()
    {
        $this->loadModuleViewsFrom(__DIR__ . '/../Resources/views', 'field');
        $this->registerConfig();
        $this->extendValidator();
        $this->registerBaseFields();
        $this->registerFieldDisplayers();
        BundleFieldModel::observe(BundleFieldObserver::class);
        BundleFieldValue::observe(BundleFieldValueObserver::class);
        \PinguCaches::register('field', 'Field', config('field.cache-keys'));
        $this->registerEntities($this->entities);

    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->singleton('field.field', Field::class);
        $this->app->singleton('field.fieldDisplayer', FieldDisplayer::class);
    }

    /**
     * Extends laravel validator with custom rules
     */
    protected function extendValidator()
    {
        \Validator::extend('unique_field', BundleFieldRules::class.'@unique');
        \Validator::extend('unique_bundle_field', BundleFieldRules::class.'@uniqueMachineName');
    }

    /**
     * Registers base fields
     */
    protected function registerBaseFields()
    {
        foreach ($this->baseFields as $field) {
            $field::register();
        }
    }

    /**
     * Registers field displayers
     */
    protected function registerFieldDisplayers()
    {
        \FieldDisplayer::register($this->fieldDisplayers);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'field'
        );
        $this->replaceConfigFrom(
            __DIR__.'/../Config/modules.php', 'modules'
        );
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('field.php')
        ], 'field-config');
    }
}
