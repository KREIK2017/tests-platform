<?php

use App\Http\Controllers\Admin\AnswerController as AdminAnswerController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\TestController as AdminTestController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\TestTakingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tests', [TestTakingController::class, 'index'])->name('tests.index');
    Route::get('/tests/{test}', [TestTakingController::class, 'show'])->name('tests.show');
    Route::post('/tests/{test}/start', [TestTakingController::class, 'start'])->name('tests.start');

    Route::get('/attempts', [TestTakingController::class, 'myAttempts'])->name('attempts.index');
    Route::get('/attempts/{attempt}', [TestTakingController::class, 'showAttempt'])->name('attempts.show');
    Route::get('/attempts/{attempt}/take', [TestTakingController::class, 'take'])->name('attempts.take');
    Route::post('/attempts/{attempt}/finish', [TestTakingController::class, 'finish'])->name('attempts.finish');
});

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('tests', AdminTestController::class);

        Route::resource('tests.questions', AdminQuestionController::class)
            ->shallow()
            ->except(['index', 'show']);

        Route::resource('questions.answers', AdminAnswerController::class)
            ->shallow()
            ->except(['index', 'show']);
    });

require __DIR__.'/auth.php';
