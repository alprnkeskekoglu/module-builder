<?php

use Dawnstar\ModuleBuilder\Http\Controllers\ModuleBuilderController;

Route::middleware(['dawnstar_auth', 'default_website'])->group(function () {
    Route::prefix('module-builders')->as('module_builders.')->group(function () {
        Route::get('/', [ModuleBuilderController::class, 'index'])->name('index');
        Route::get('/getTranslations', [ModuleBuilderController::class, 'getTranslations'])->name('getTranslations');
        Route::get('/{moduleBuilder}/edit', [ModuleBuilderController::class, 'edit'])->name('edit');
        Route::put('/{moduleBuilder}', [ModuleBuilderController::class, 'update'])->name('update');
        Route::get('/{moduleBuilder}/getBuilderData', [ModuleBuilderController::class, 'getBuilderData']);
    });
});
