<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SMSController;

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

//Login and register API's, not authenticated
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

//Secure API's need auth token
Route::group(['middleware' => 'auth:sanctum'], function () {
    //Logout API
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    //Send SMS API's
    Route::group(['prefix' => 'sms'], function() {
        Route::post('single', [SMSController::class, 'sendSingleSMS']);
        Route::post('multiple', [SMSController::class, 'sendMultipleSMS']);
        Route::get('/', [SMSController::class, 'index']);
        Route::get('/{id}', [SMSController::class, 'show']);
    });
});
