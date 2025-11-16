<?php
declare(strict_types=1);

use App\Domain\Orders\Http\Controllers\AnalyticsController;
use App\Domain\Orders\Http\Controllers\OrdersController;
use App\Domain\Orders\Http\Controllers\TransactionsController;

Route::prefix('stores/orders')->group(function () {
    // @deprecated - this is only meant for quick and dirty feature request from Shopify.  Don't use this API.
    Route::post('/{id}/end_trial_before_expiry', [OrdersController::class, 'endTrialBeforeExpiry'])
        ->name('endTrialBeforeExpiry');

    Route::get('/analytics', [AnalyticsController::class, 'get'])
        ->name('getOrdersAnalytics');

    Route::get('/transactions', [TransactionsController::class, 'get'])
        ->name('getOrdersTransactions');

    Route::get('/{id}', [OrdersController::class, 'get'])->name('get');
});
