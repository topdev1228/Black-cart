<?php
declare(strict_types=1);

use App\Domain\Orders\Http\Controllers\OrdersController;

Route::prefix('orders')->group(function () {
    Route::get('{id}/end_trial_before_expiry', [OrdersController::class, 'endTrialForm'])->name('view.orders.end-early');
});
