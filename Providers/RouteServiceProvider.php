<?php

namespace Pingu\Field\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The root namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $namespace = 'Pingu\Field\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapAjaxRoutes();

        $this->mapWebRoutes();

        $this->mapAdminRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware(['web', 'permission:browse site'])
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/web.php');
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless and are prefixed with 'api'
     *
     * @return void
     */
    protected function mapAjaxRoutes()
    {
        Route::prefix('ajax')
            ->middleware('ajax')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/ajax.php');
    }

    /**
     * Define the "admin web" routes for the application.
     *
     * These routes belongs to "web" middleware group and are subject to 'access admin area' permissions
     * They are prefixed with 'admin' 
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::middleware(['web', 'permission:access admin area'])
            ->prefix('admin')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/../Routes/admin.php');
    }
}
