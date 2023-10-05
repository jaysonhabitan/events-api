<?php

use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\LoginController;
use Illuminate\Http\Response;
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



Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [LoginController::class, 'Login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('events', EventController::class);
    });
});

Route::get('/', function() {
    return Response::HTTP_OK;
});
