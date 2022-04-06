<?php

use Dawnstar\ModuleBuilder\Http\Controllers\ModuleBuilderController;

Route::controller(ModuleBuilderController::class)->prefix('module-builders')->as('module_builders.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/getTranslations', 'getTranslations')->name('getTranslations');
    Route::prefix('{moduleBuilder}')->group(function () {
        Route::get('/edit', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
        Route::get('/getBuilderData', 'getBuilderData')->name('edit');
    });
});
