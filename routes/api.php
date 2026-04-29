<?php

use App\Http\Controllers\Api\V1\AnswerController;
use App\Http\Controllers\Api\V1\AttemptController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\TestController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/email/verification-notification', [AuthController::class, 'sendVerification'])
            ->middleware('throttle:6,1');

        Route::middleware('verified')->group(function () {
            Route::apiResource('tests', TestController::class);

            Route::apiResource('tests.questions', QuestionController::class)
                ->shallow();

            Route::apiResource('questions.answers', AnswerController::class)
                ->shallow();

            Route::post('/tests/{test}/attempts', [AttemptController::class, 'start']);
            Route::post('/attempts/{attempt}/finish', [AttemptController::class, 'finish']);
            Route::get('/attempts', [AttemptController::class, 'index']);
            Route::get('/attempts/{attempt}', [AttemptController::class, 'show']);
        });
    });
});
