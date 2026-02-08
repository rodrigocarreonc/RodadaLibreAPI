<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ChangeRequestController;

Route::prefix('v1')->group(function (){
    Route::get('/places', [PlaceController::class, 'index']);

    Route::group(['middleware' => 'auth:api'],function ($router){
        Route::post('/places', [PlaceController::class, 'store']);
        Route::put('/places/{place}', [PlaceController::class, 'update']);
        Route::delete('/places/{place}', [PlaceController::class, 'destroy']);
        
        Route::post('/photos/upload', [PhotoController::class, 'upload']);

        Route::middleware(['role:admin'])->prefix('admin')->group(function(){
            Route::get('/requests', [ChangeRequestController::class, 'index']);
            Route::post('/requests/{id}/approve', [ChangeRequestController::class, 'approve']);
            Route::post('/requests/{id}/reject', [ChangeRequestController::class, 'reject']);
        });
    });

    Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/me', [AuthController::class, 'me']);
    });
});