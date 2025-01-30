<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SmtpConfigController;
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

    Route::middleware([JwtMiddleware::class, 'role:admin'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);

        // SMTP Routes
        Route::apiResource('smtp', SmtpConfigController::class)->only(['store', 'update', 'show', 'index']);

        // Company Routes
        Route::apiResource('/companies', CompanyController::class);

        // Email Routes
        Route::post('/send-email', [EmailController::class, 'sendEmail']);
        Route::apiResource('emails',EmailController::class)->only([
            'store', 'index', 'update'
        ]);

        // Documents Routes
//        Route::prefix('documents')->group(function () {
//            Route::get('/', [FileController::class, 'index']);
//            Route::post('/', [FileController::class, 'store']);
//            Route::get('download/{file}', [FileController::class, 'download'])
//                ->name('file.download')
//                ->middleware('signed') // Ensure signed middleware is applied
//                ->withoutMiddleware('role:student'); // Exclude the role middleware for this route
//            Route::delete('{file}', [FileController::class, 'destroy']);
//        });
    });
});
