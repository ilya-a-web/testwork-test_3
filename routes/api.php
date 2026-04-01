<?php

use App\Http\Controllers\Api\EpisodeController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/episodes', [EpisodeController::class, 'index']);
Route::post('/episodes/{episode}/reviews', [ReviewController::class, 'store']);
