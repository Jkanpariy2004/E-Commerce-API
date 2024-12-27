<?php

use App\Http\Controllers\Admin\ArticlesController;
use App\Http\Controllers\Admin\Auth\AuthtenticationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MembersController;
use App\Http\Controllers\Admin\ProjectsController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\TestimonialsController;
use App\Http\Controllers\Frontend\ArticleController;
use App\Http\Controllers\Frontend\MemberController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthtenticationController::class, 'Authenticate']);

// services frontend api
Route::get('/get-services', [ServiceController::class, 'index']);
Route::get('/service/{slug}', [ServiceController::class, 'slug']);
Route::get('/get-latest-services', [ServiceController::class, 'latestServices']);

// projects frontend api
Route::get('/get-projects', [ProjectController::class, 'index']);
Route::get('/project/{slug}', [ProjectController::class, 'slug']);
Route::get('/get-latest-projects', [ProjectController::class, 'latestServices']);

// articles frontend api
Route::get('/get-articles', [ArticleController::class, 'index']);
Route::get('/get-latest-articles', [ArticleController::class, 'latestArticles']);

// testimonials frontend api
Route::get('/get-testimonials', [TestimonialController::class, 'index']);

//members frontend api
Route::get('/get-members', [MemberController::class, 'index']);

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

    // Testimonial api
    Route::prefix('testimonial')->controller(TestimonialsController::class)->group(function () {
        Route::get('/get', 'index');
        Route::get('/get/{id}', 'show');
        Route::post('/insert', 'store');
        Route::post('/update/{id}', 'update');
        Route::get('/delete/{id}', 'destroy');
    });

    // members api
    Route::prefix('members')->controller(MembersController::class)->group(function () {
        Route::get('/get', 'index');
        Route::get('/get/{id}', 'show');
        Route::post('/insert', 'store');
        Route::post('/update/{id}', 'update');
        Route::get('/delete/{id}', 'destroy');
    });
});
