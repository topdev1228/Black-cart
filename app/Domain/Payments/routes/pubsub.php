<?php
declare(strict_types=1);

use App\Domain\Payments\Http\Controllers\PubSubController;

Route::post('payments', [PubSubController::class, 'post'])->name('message');
