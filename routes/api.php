<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SMTPController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\RefreshTokenMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        // Authentication routes
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        // Password management
        Route::post('forget', [AuthController::class, 'forget']);
        Route::post('validate', [AuthController::class, 'validateCode']);
        Route::post('reset', [AuthController::class, 'resetPassword']);

        // Token management
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware([RefreshTokenMiddleware::class]);
        Route::post('logout', [AuthController::class, 'logout'])->middleware([JwtMiddleware::class]);
    });

    Route::middleware([JwtMiddleware::class])->group(function () {
        Route::get('me', [AuthController::class, 'me']);

        // Company Routes
        Route::post('/companies', [CompanyController::class, 'store']);
        Route::get('/companies', [CompanyController::class, 'index']);
        Route::get('/companies/{company}', [CompanyController::class, 'show']);
        Route::put('/companies/{company}', [CompanyController::class, 'update']);
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);

        // SMTP Routes
        Route::post('/companies/{company}/smtp', [SMTPController::class, 'store']);
        Route::get('/companies/{company}/smtp', [SMTPController::class, 'show']);

        // Email Routes
        Route::post('/companies/{company}/send-email', [EmailController::class, 'send']);
        Route::get('/companies/{company}/emails', [EmailController::class, 'index']);
        Route::get('/emails/{id}', [EmailController::class, 'show']);

        // File System Routes
//    Route::post('/companies/{id}/files', [FileController::class, 'upload']);
//    Route::get('/companies/{id}/files', [FileController::class, 'index']);
//    Route::delete('/files/{id}', [FileController::class, 'destroy']);
    });
});


