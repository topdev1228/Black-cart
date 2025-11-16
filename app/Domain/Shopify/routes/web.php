<?php
declare(strict_types=1);

use App\Domain\Shared\Http\Middleware\AuthenticateShopifyHttpWebhook;
use App\Domain\Shared\Http\Middleware\LogShopifyWebhook;
use App\Domain\Shopify\Http\Controllers\ShopifyController;
use App\Domain\Shopify\Http\Controllers\WebhooksController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth/shopify')
    ->group(function () {
        Route::get('redirect', [ShopifyController::class, 'redirect'])->name('redirect');
        Route::get('callback', [ShopifyController::class, 'callback'])->name('callback');
    });

// POST https://shop-app.blackcart.com/webhooks/*
Route::prefix('webhooks')
    ->middleware([AuthenticateShopifyHttpWebhook::class, LogShopifyWebhook::class])
    ->group(
        function () {
            Route::post('customers_data_request', [WebhooksController::class, 'customers_data_request'])->name('customers_data_request');
            Route::post('customers_redact', [WebhooksController::class, 'customers_redact'])->name('customers_redact');
            Route::post('shop_redact', [WebhooksController::class, 'shop_redact'])->name('shop_redact');
        }
    );
