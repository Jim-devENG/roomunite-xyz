<?php

use Illuminate\Support\Facades\Route;
use Infoamin\Installer\Http\Controllers\InstallerController;

Route::group(['prefix' => 'install', 'middleware' => ['web']], function () {
    Route::get('/', [InstallerController::class, 'welcome'])->name('LaravelInstaller::welcome');
    Route::get('requirements', [InstallerController::class, 'requirements'])->name('LaravelInstaller::requirements');
    Route::get('permissions', [InstallerController::class, 'permissions'])->name('LaravelInstaller::permissions');
    Route::get('database', [InstallerController::class, 'database'])->name('LaravelInstaller::database');
    Route::get('purchasecode', [InstallerController::class, 'purchasecode'])->name('LaravelInstaller::purchasecode');
    Route::get('register', [InstallerController::class, 'register'])->name('LaravelInstaller::register');
    Route::get('finish', [InstallerController::class, 'finish'])->name('LaravelInstaller::finish');
    
    Route::post('database', [InstallerController::class, 'saveDatabase'])->name('LaravelInstaller::saveDatabase');
    Route::post('purchasecode', [InstallerController::class, 'savePurchaseCode'])->name('LaravelInstaller::savePurchaseCode');
    Route::post('register', [InstallerController::class, 'saveRegister'])->name('LaravelInstaller::saveRegister');
});

