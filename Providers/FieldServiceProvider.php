<?php

namespace Pingu\Field\Providers;

use Illuminate\Database\Eloquent\Factory;
use Pingu\Core\Support\ModuleServiceProvider;
use Pingu\Field\BaseFields\{Boolean, Datetime, Email, Integer, LongText, ManyModel, Model, Password, Text, _Float, _List};
use Pingu\Field\Displayers\DefaultTextDisplayer;
use Pingu\Field\Displayers\FakeDisplayer;
use Pingu\Field\Displayers\TrimmedTextDisplayer;
use Pingu\Field\Entities\{BundleField as BundleFieldModel, BundleFieldValue, FieldBoolean, FieldDatetime, FieldEmail, FieldEntity, FieldFloat, FieldInteger, FieldText, FieldTextLong, FieldTime, FieldUrl};
use Pingu\Field\Field;
use Pingu\Field\FieldDisplay;
use Pingu\Field\Observers\BundleFieldObserver;
use Pingu\Field\Observers\BundleFieldValueObserver;
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
    protected $bundleFields = [
        FieldBoolean::class,
        FieldDatetime::class,
        FieldEmail::class,
        FieldFloat::class,
        FieldInteger::class,
        FieldText::class,
        FieldTextLong::class,
        FieldUrl::class,
        FieldTime::class,
        FieldEntity::class
    ];

    /**
     * List of field displayer
     * @var array
     */
    protected $fieldDisplayers = [
        FakeDisplayer::class,
        TrimmedTextDisplayer::class,
        DefaultTextDisplayer::class
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        \ModelRoutes::registerSlugFromObject(new BundleFieldModel);
        $this->registerConfig();
        $this->extendValidator();
        $this->registerBundleFields();
        $this->registerBaseFields();
        $this->registerFieldDisplayers();
        BundleFieldModel::observe(BundleFieldObserver::class);
        BundleFieldValue::observe(BundleFieldValueObserver::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->singleton('field.field', Field::class);
        $this->app->singleton('field.display', FieldDisplay::class);
    }

    protected function extendValidator()
    {
        \Validator::extend('unique_field', BundleFieldRules::class.'@unique');
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
     * Registers bundle fields in Field facade
     */
    protected function registerBundleFields()
    {
        \Field::registerBundleFields($this->bundleFields);
    }

    /**
     * Registers field displayers
     */
    protected function registerFieldDisplayers()
    {
        \FieldDisplay::registerDisplayers($this->fieldDisplayers);
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
