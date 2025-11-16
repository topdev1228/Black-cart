<?php
declare(strict_types=1);

use App\Domain\Programs\Http\Controllers\PubSubController;

Route::post('stores/programs', [PubSubController::class, 'post'])->name('message');
