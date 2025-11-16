<?php
declare(strict_types=1);

use App\Domain\Programs\Http\Controllers\ProgramController;
use App\Domain\Programs\Http\Controllers\ProgramProductsController;

Route::prefix('stores')->group(function () {
    Route::get('programs', [ProgramController::class, 'index'])->name('index');
    Route::post('programs', [ProgramController::class, 'store'])->name('store');
    Route::put('programs/{id}', [ProgramController::class, 'update'])->name('update');
    Route::post('programs/{id}/variants', [ProgramController::class, 'addVariants'])->name('addVariants');
    Route::delete('programs/{id}/variants', [ProgramController::class, 'removeVariants'])->name('removeVariants');
    Route::get('programs/{id}/variants', [ProgramController::class, 'variantsInSellingPlan'])->name('variantsInSellingPlan');
    Route::delete('programs/{id}/products', [ProgramController::class, 'removeProducts'])->name('removeProducts');
    Route::get('programs/{id}/products', [ProgramProductsController::class, 'getProducts'])->name('getProducts');
});
