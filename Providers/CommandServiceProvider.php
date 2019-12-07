<?php

namespace Pingu\Field\Providers;

use Illuminate\Support\ServiceProvider;
use Pingu\Field\Console\ModuleMakeEntityFields;
use Pingu\Field\Console\ModuleMakeEntityValidator;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    protected $commands = [
        'command.moduleMakeEntityFields',
        'command.moduleMakeEntityValidator'
    ];
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }
    /**
     * Registers the serve command
     */
    protected function registerCommands()
    {
        $this->app->bind(
            'command.moduleMakeEntityFields', function ($app) {
                return new ModuleMakeEntityFields();
            }
        );
        $this->app->bind(
            'command.moduleMakeEntityValidator', function ($app) {
                return new ModuleMakeEntityValidator();
            }
        );
        $this->commands($this->commands);
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return $this->commands;
    }
}