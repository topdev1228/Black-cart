<?php
declare(strict_types=1);

use App\Domain\Stores\Http\Controllers\StoreController;
use App\Domain\Stores\Http\Controllers\StoreSettingController;

Route::prefix('stores')->group(function () {
    Route::get('', [StoreController::class, 'index'])->name('index');
    Route::post('', [StoreController::class, 'create'])->name('create');
    Route::put('', [StoreController::class, 'update'])->name('update');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('', [StoreSettingController::class, 'index'])->name('index');
        Route::patch('', [StoreSettingController::class, 'save'])->name('save');
    });
});
