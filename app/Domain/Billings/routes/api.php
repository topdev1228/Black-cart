<?php
declare(strict_types=1);

use App\Domain\Billings\Http\Controllers\ShopifyCurrentAppInstallationsController;
use App\Domain\Billings\Http\Controllers\SubscriptionsController;

Route::prefix('stores/billings')->group(function () {
    Route::post('subscriptions', [SubscriptionsController::class, 'store'])->name('store');
    Route::get('subscriptions/active', [SubscriptionsController::class, 'getActive'])->name('getActive');

    Route::get('subscriptions/shopify_current_app_installation', [ShopifyCurrentAppInstallationsController::class, 'get'])
        ->name('getCurrentAppInstallation');
});
