<?php
declare(strict_types=1);

use App\Domain\Shopify\Http\Controllers\PubSubController;

Route::post('shopify', [PubSubController::class, 'post'])->name('message');
