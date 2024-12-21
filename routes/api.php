<?php

use App\Http\Controllers\Admin\ArticlesController;
use App\Http\Controllers\Admin\Auth\AuthtenticationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectsController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Frontend\ServiceController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthtenticationController::class, 'Authenticate']);
Route::get('/get-services', [ServiceController::class, 'index']);
Route::get('/get-latest-services', [ServiceController::class, 'latestServices']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/logout', [AuthtenticationController::class, 'Logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // services api
    Route::prefix('services')->controller(ServicesController::class)->group(function () {
        Route::get('/get', 'index');
        Route::get('/get/{id}', 'show');
        Route::post('/insert', 'store');
        Route::post('/update/{id}', 'update');
        Route::get('/delete/{id}', 'destroy');
    });

    // project api
    Route::prefix('projects')->controller(ProjectsController::class)->group(function () {
        Route::get('/get', 'index');
        Route::get('/get/{id}', 'show');
        Route::post('/insert', 'store');
        Route::post('/update/{id}', 'update');
        Route::get('/delete/{id}', 'destroy');
    });

    // blog api
    Route::prefix('blogs')->controller(ArticlesController::class)->group(function () {
        Route::get('/get', 'index');
        Route::get('/get/{id}', 'show');
        Route::post('/insert', 'store');
        Route::post('/update/{id}', 'update');
        Route::get('/delete/{id}', 'destroy');
    });
});
