<?php
declare(strict_types=1);

use App\Domain\Products\Http\Controllers\ProductsController;

Route::prefix('stores')->group(function () {
    Route::get('products/{id}', [ProductsController::class, 'get'])->name('get');
});
