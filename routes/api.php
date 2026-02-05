<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AuthController;

Route::prefix('v1')->group(function (){
    Route::get('/places', [PlaceController::class, 'index']);

    Route::post('/places', [PlaceController::class, 'store']);
    Route::post('/photos/upload', [PhotoController::class, 'upload']);
    
    Route::put('/places/{place}', [PlaceController::class, 'update']);
    Route::delete('/places/{place}', [PlaceController::class, 'destroy']);

    Route::group([
        'middleware' => 'api',
        'prefix' => 'auth'
    ], function ($router) {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/me', [AuthController::class, 'me']);
    });
});