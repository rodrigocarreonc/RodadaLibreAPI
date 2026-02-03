<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlaceController;

Route::get('/places', [PlaceController::class, 'index']);

Route::post('/places', [PlaceController::class, 'store']);