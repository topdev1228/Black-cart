<?php
declare(strict_types=1);

use App\Domain\Shared\Http\Controllers\AppVerifyController;
use App\Domain\Shared\Http\Middleware\JwtAuthentication;
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

Route::prefix('app')->name('app.')->withoutMiddleware(JwtAuthentication::class)
    ->group(function () {
        Route::get('secrets', [AppVerifyController::class, 'secrets'])->name('secrets');
        Route::get('headers', [AppVerifyController::class, 'headers'])->name('headers');
        Route::get('log', function () {
            Log::info('Log Test', ['test' => 1]);
        })->name('log');
    });
