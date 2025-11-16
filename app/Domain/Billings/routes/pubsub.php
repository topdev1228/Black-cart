<?php
declare(strict_types=1);

use App\Domain\Billings\Http\Controllers\PubSubController;

Route::post('stores/billings', [PubSubController::class, 'post'])->name('message');
