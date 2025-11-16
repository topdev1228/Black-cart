<?php
declare(strict_types=1);

use App\Domain\Trials\Http\Controllers\PubSubController;

Route::post('trials', [PubSubController::class, 'post'])->name('message');
