<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('csrf-token', fn () => new \Illuminate\Http\JsonResponse(['token' => csrf_token()]))->name('api.csrf-token');
});
