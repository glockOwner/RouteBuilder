<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
    Route::post('/registration', 'registerUser');
    Route::post('/login', 'loginUser') ;
});

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['controller' => \App\Http\Controllers\AuthController::class], function () {
        Route::post('/refresh-token', 'refreshToken')->name('token.refresh');
    });
    Route::group(['controller' => \App\Http\Controllers\RouteController::class], function () {
        Route::post('/route/save-coordinates', 'saveCoordinates');
        Route::get('/route', 'getRoute');
    });
});
