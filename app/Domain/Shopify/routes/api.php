<?php
declare(strict_types=1);

use App\Domain\Shopify\Http\Controllers\JobsController;

Route::prefix('shopify')->group(function () {
    Route::post('jobs', [JobsController::class, 'store'])->name('store');
});
