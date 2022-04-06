<?php

namespace Dawnstar\ModuleBuilder\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapPanelRoutes();
    }

    protected function mapPanelRoutes()
    {
        Route::group(['middleware' => ['web', 'dawnstar_auth', 'default_website'], 'prefix' => 'dawnstar', 'as' => 'dawnstar.'], function ($router) {
            require __DIR__ . '/../Routes/panel.php';
        });
    }
}
