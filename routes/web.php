<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\PreferencesController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TimeEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::patch('clients/{client}', [ClientController::class, 'update']);
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::patch('projects/{project}', [ProjectController::class, 'update']);
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::get('time-entries', [TimeEntryController::class, 'index'])->name('time-entries.index');
    Route::post('time-entries', [TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::put('time-entries/{timeEntry}', [TimeEntryController::class, 'update'])->name('time-entries.update');
    Route::patch('time-entries/{timeEntry}', [TimeEntryController::class, 'update']);
    Route::delete('time-entries/{timeEntry}', [TimeEntryController::class, 'destroy'])->name('time-entries.destroy');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('settings', [SettingsController::class, 'show'])
        ->name('settings');

    Route::prefix('settings')->as('settings.')->group(function () {
        Route::singleton('profile', ProfileController::class)->only(['edit', 'update']);
        Route::get('profile/delete', [ProfileController::class, 'delete'])->name('profile.delete');
        Route::post('profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::singleton('password', PasswordController::class)->only(['edit', 'update']);
        Route::singleton('preferences', PreferencesController::class)->only(['edit', 'update']);
    });
});
