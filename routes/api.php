<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlaceController;
use App\Http\Controllers\PhotoController;

Route::get('/places', [PlaceController::class, 'index']);

Route::post('/places', [PlaceController::class, 'store']);
Route::post('/photos/upload', [PhotoController::class, 'upload']);