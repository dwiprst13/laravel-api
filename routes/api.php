<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CommentReportController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SavedPostController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('token', 'token');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', 'logout');
            Route::get('me', 'me');
        });
    });

    // Route::get('test', function () {
    //     return 'ok';
    // });

    Route::get('settings', [SettingController::class, 'show']);

    Route::controller(PostController::class)->group(function () {
        Route::get('posts', 'index');
        Route::get('posts/{post}', 'show');
        Route::get('posts/{post}/recommendations', 'recommendations');
    });

    Route::get('posts/{post}/comments', [CommentController::class, 'index']);

    Route::post('messages', [MessageController::class, 'store']);


    // Routes for authenticated uswrs
    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(ProfileController::class)->group(function () {
            Route::get('profile', 'show');
            Route::match(['put', 'patch'], 'profile', 'update');
        });

        Route::prefix('posts/{post}')->group(function () {
            Route::post('comments', [CommentController::class, 'store']);

            Route::controller(LikeController::class)->group(function () {
                Route::post('like', 'store');
                Route::delete('like', 'destroy');
            });

            Route::controller(SavedPostController::class)->group(function () {
                Route::post('save', 'store');
                Route::delete('save', 'destroy');
            });
        });

        Route::controller(CommentController::class)->group(function () {
            Route::patch('comments/{comment}', 'update');
            Route::delete('comments/{comment}', 'destroy');
        });

        Route::post('comments/{comment}/report', [CommentReportController::class, 'store']);

        Route::get('me/saved-posts', [SavedPostController::class, 'index']);
    });


    // Routes foir admin roles
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        Route::controller(PostController::class)->group(function () {
            Route::post('posts', 'store');
            Route::match(['put', 'patch'], 'posts/{post}', 'update');
            Route::delete('posts/{post}', 'destroy');
        });

        Route::get('dashboard', DashboardController::class);

        Route::controller(CommentReportController::class)->prefix('comment-reports')->group(function () {
            Route::get('/', 'index');
            Route::patch('{commentReport}', 'update');
        });

        Route::put('settings', [SettingController::class, 'update']);

        Route::controller(MessageController::class)->prefix('messages')->group(function () {
            Route::get('/', 'index');
            Route::get('{message}', 'show');
            Route::delete('{message}', 'destroy');
        });

        Route::controller(UserManagementController::class)->prefix('users')->group(function () {
            Route::get('/', 'index');
            Route::get('{user}', 'show');
            Route::match(['put', 'patch'], '{user}', 'update');
        });

        Route::controller(MediaController::class)->prefix('media')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('{media}', 'show');
            Route::match(['put', 'patch'], '{media}', 'update');
            Route::delete('{media}', 'destroy');
        });

        Route::controller(CategoryController::class)->prefix('categories')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('{category}', 'show');
            Route::match(['put', 'patch'], '{category}', 'update');
            Route::delete('{category}', 'destroy');
        });

        Route::controller(TagController::class)->prefix('tags')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('{tag}', 'show');
            Route::match(['put', 'patch'], '{tag}', 'update');
            Route::delete('{tag}', 'destroy');
        });
    });
});
