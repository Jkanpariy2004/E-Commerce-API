<?php

use App\Http\Controllers\Admin\Auth\AuthtenticationController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthtenticationController::class, 'Authenticate']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/logout', [AuthtenticationController::class, 'Logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
});
