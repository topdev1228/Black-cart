<?php
declare(strict_types=1);

use App\Domain\Trials\Http\Controllers\TrialsController;

Route::resource('trials', TrialsController::class);
Route::prefix('trials')->group(function () {
});
