<?php

namespace Pingu\Field\Providers;

use Illuminate\Database\Eloquent\Factory;
use Pingu\Core\Support\ModuleServiceProvider;
use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Datetime;
use Pingu\Field\BaseFields\Email;
use Pingu\Field\BaseFields\Integer;
use Pingu\Field\BaseFields\LongText;
use Pingu\Field\BaseFields\ManyModel;
use Pingu\Field\BaseFields\Media;
use Pingu\Field\BaseFields\Model;
use Pingu\Field\BaseFields\Password;
use Pingu\Field\BaseFields\Text;
use Pingu\Field\BaseFields\_Float;
use Pingu\Field\BaseFields\_List;
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
        Media::class,
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
        FieldDate::class,
        FieldDatetime::class,
        FieldEmail::class,
        FieldFloat::class,
        FieldInteger::class,
        FieldText::class,
        FieldTextLong::class,
        FieldUrl::class
    ];
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->extendValidator();
        \ModelRoutes::registerSlugFromObject(new BundleFieldModel);
        $this->registerBundleFields();
        $this->registerBaseFieldWidgets();
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

    /**
     * Registers base fields widget in the form field facade
     */
    protected function registerBaseFieldWidgets()
    {
        foreach ($this->baseFields as $field) {
            $field::registerWidgets();
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
            __DIR__.'/../Config/config.php' => config_path('module-field.php')
        ], 'config');
    }
}
