<?php
declare(strict_types=1);

use App\Domain\Stores\Http\Controllers\PubSubController;

Route::post('stores', [PubSubController::class, 'post'])->name('message');
