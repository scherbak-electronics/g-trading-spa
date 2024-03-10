<?php

use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/sanctum/token', TokenController::class);

Route::middleware(['auth:sanctum', 'apply_locale'])->group(function () {

    /**
     * Auth related
     */
    Route::get('/users/auth', AuthController::class);

    /**
     * Users
     */
    Route::put('/users/{user}/avatar', [UserController::class, 'updateAvatar']);
    Route::resource('users', UserController::class);

    /**
     * Roles
     */
    Route::get('/roles/search', [RoleController::class, 'search'])->middleware('throttle:400,1');

    /**
     * Trading
     */
    Route::get('/trading/exchange/kline', [ExchangeController::class, 'kline']);
    Route::get('/trading/exchange/info', [ExchangeController::class, 'info']);
    Route::get('/trading/exchange/symbols', [ExchangeController::class, 'symbols']);
    Route::get('/trading/exchange/timeframes', [ExchangeController::class, 'timeframes']);
    Route::get('/trading/exchange/strategies', [ExchangeController::class, 'strategies']);
    Route::get('/trading/exchange/symbol/info', [ExchangeController::class, 'symbolInfo']);
    Route::get('/trading/exchange/ticker24h', [ExchangeController::class, 'ticker24h']);
    Route::get('/trading/exchange/priceTicker', [ExchangeController::class, 'priceTicker']);
    Route::get('/trading/exchange/updateLastBar', [ExchangeController::class, 'updateLastBar']);
    Route::get('/trading/exchange/updateExchangeInfo', [ExchangeController::class, 'updateExchangeInfo']);
    Route::get('/trading/exchange/getSymbolMinPrice', [ExchangeController::class, 'getSymbolMinPrice']);
    Route::get('/trading/exchange/getOpenOrders', [ExchangeController::class, 'getOpenOrders']);
    Route::get('/trading/exchange/getAllOrders', [ExchangeController::class, 'getAllOrders']);
    Route::get('/trading/exchange/getOrder', [ExchangeController::class, 'getOrder']);
    Route::get('/trading/session/get', [SessionController::class, 'getSession']);

    Route::resource('homework', HomeworkController::class);
    Route::resource('trading/session', SessionController::class)->only(['store', 'update']);
    Route::patch('trading/session/update/{session}', [SessionController::class, 'update']);
    Route::patch('trading/session/{session}/start', [SessionController::class, 'start']);
    Route::patch('trading/session/{session}/stop', [SessionController::class, 'stop']);
});
