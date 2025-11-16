<?php
declare(strict_types=1);

use App\Domain\Orders\Http\Controllers\PubSubController;

Route::post('orders', [PubSubController::class, 'post'])->name('message');
