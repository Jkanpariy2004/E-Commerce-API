<?php

use App\Http\Controllers\Admin\ArticlesController;
use App\Http\Controllers\Admin\Auth\AuthtenticationController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthtenticationController::class, 'Authenticate']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/logout', [AuthtenticationController::class, 'Logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::prefix('blogs')->controller(ArticlesController::class)->group(function () {
        Route::get('/get', 'index');
        Route::get('/get/{id}', 'show');
        Route::post('/insert', 'store');
        Route::post('/update/{id}', 'update');
        Route::get('/delete/{id}', 'destroy');
    });
});
