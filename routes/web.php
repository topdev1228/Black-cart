<?php
declare(strict_types=1);

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::name('internal.')->group(function () {
    Route::get('/liveness_check', function () {
        return response('Application Live', Response::HTTP_OK);
    })->name('liveness-check');

    Route::get('/readiness_check', function () {
        return response('Application Live', Response::HTTP_OK);
    })->name('readiness-check');
});

Route::get('/', function () {
    return view('app');
})->middleware(['shopify.auth', 'shopify.installed']);

Route::get('/{path?}', function () {
    return view('app');
})->where('path', '.*')->middleware(['shopify.auth', 'shopify.installed']);
