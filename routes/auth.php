<?php

use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\SessionsController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [SessionsController::class, 'create'])
        ->name('login');

    Route::post('login', [SessionsController::class, 'store'])
        ->name('login.store');

    Route::post('logout', [SessionsController::class, 'destroy'])
        ->name('logout');

    Route::get('register', [RegistrationController::class, 'create'])
        ->middleware('single.user')
        ->name('register');

    Route::post('register', [RegistrationController::class, 'store'])
        ->middleware('single.user')
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [VerifyEmailController::class, 'show'])
        ->name('verification.notice');

    Route::post('verify-email', [VerifyEmailController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, 'update'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', [ConfirmPasswordController::class, 'create'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.confirm.store');
});

Route::post('logout', [SessionsController::class, 'destroy'])
    ->name('logout');
