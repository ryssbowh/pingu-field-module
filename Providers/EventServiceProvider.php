<?php

namespace Pingu\Field\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Pingu\Field\Listeners\DashifyMachineNameField;
use Pingu\Forms\Events\FormBuilt;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        FormBuilt::class => [DashifyMachineNameField::class],
    ];
}