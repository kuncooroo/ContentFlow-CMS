<?php

use App\Http\Controllers\Install\InstallController;
use App\Http\Middleware\EnsureInstallerUnlocked;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', EnsureInstallerUnlocked::class])
    ->prefix('install')
    ->name('install.')
    ->group(function (): void {
        Route::get('/', [InstallController::class, 'requirements'])->name('requirements');
        Route::post('/requirements', [InstallController::class, 'storeRequirements'])
            ->middleware('throttle:10,1')
            ->name('requirements.store');

        Route::get('/application', [InstallController::class, 'application'])->name('application');
        Route::post('/application', [InstallController::class, 'storeApplication'])
            ->middleware('throttle:10,1')
            ->name('application.store');

        Route::get('/database', [InstallController::class, 'database'])->name('database');
        Route::post('/database', [InstallController::class, 'storeDatabase'])
            ->middleware('throttle:6,1')
            ->name('database.store');

        Route::get('/administrator', [InstallController::class, 'administrator'])->name('administrator');
        Route::post('/administrator', [InstallController::class, 'storeAdministrator'])
            ->middleware('throttle:10,1')
            ->name('administrator.store');

        Route::get('/settings', [InstallController::class, 'settings'])->name('settings');
        Route::post('/settings', [InstallController::class, 'storeSettings'])
            ->middleware('throttle:10,1')
            ->name('settings.store');
    });

Route::middleware('web')
    ->get('/install/complete', [InstallController::class, 'complete'])
    ->name('install.complete');
