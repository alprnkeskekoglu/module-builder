<?php

namespace Dawnstar\ModuleBuilder;

use Dawnstar\ModuleBuilder\Providers\RouteServiceProvider;
use Illuminate\Support\ServiceProvider;

class ModuleBuilderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'ModuleBuilder');
        $this->loadTranslationsFrom(__DIR__ . '/Lang', 'ModuleBuilder');

        $this->publishes([__DIR__ . '/Assets' => public_path('vendor/dawnstar/module_builder')], 'dawnstar-module-builder-assets');
    }
}
