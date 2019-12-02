<?php

namespace Pingu\Field\Providers;

use Illuminate\Database\Eloquent\Factory;
use Pingu\Core\Support\ModuleServiceProvider;
use Pingu\Field\Entities\BundleField as BundleFieldModel;
use Pingu\Field\Entities\FieldBoolean;
use Pingu\Field\Entities\FieldDate;
use Pingu\Field\Entities\FieldDatetime;
use Pingu\Field\Entities\FieldEmail;
use Pingu\Field\Entities\FieldFloat;
use Pingu\Field\Entities\FieldInteger;
use Pingu\Field\Entities\FieldText;
use Pingu\Field\Entities\FieldTextLong;
use Pingu\Field\Entities\FieldUrl;
use Pingu\Field\Field;
use Pingu\Field\Validation\BundleFieldRules;

class FieldServiceProvider extends ModuleServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerBundleFields();
        $this->extendValidator();
        \ModelRoutes::registerSlugFromObject(new BundleFieldModel);
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
    }

    protected function extendValidator()
    {
        \Validator::extend('unique_field', BundleFieldRules::class.'@unique');
    }

    protected function registerBundleFields()
    {
        \Field::registerBundleFields(
            [
                FieldBoolean::class,
                FieldDate::class,
                FieldDatetime::class,
                FieldEmail::class,
                FieldFloat::class,
                FieldInteger::class,
                FieldText::class,
                FieldTextLong::class,
                FieldUrl::class
            ]
        );
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
    }
}
