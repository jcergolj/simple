<?php

use App\Http\Controllers\Turbo\ClientController;
use App\Http\Controllers\Turbo\ClientSearchController;
use App\Http\Controllers\Turbo\ProjectController;
use App\Http\Controllers\Turbo\ProjectSearchController;
use App\Http\Controllers\Turbo\RunningTimerSessionController;
use App\Http\Controllers\Turbo\TimeEntryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->as('turbo.')->group(function () {
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::patch('clients/{client}', [ClientController::class, 'update']);

    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::patch('projects/{project}', [ProjectController::class, 'update']);

    Route::get('time-entries/create', [TimeEntryController::class, 'create'])->name('time-entries.create');
    Route::post('time-entries', [TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::get('time-entries/{time_entry}/edit', [TimeEntryController::class, 'edit'])->name('time-entries.edit');
    Route::put('time-entries/{timeEntry}', [TimeEntryController::class, 'update'])->name('time-entries.update');
    Route::patch('time-entries/{timeEntry}', [TimeEntryController::class, 'update']);

    Route::get('clients-search', ClientSearchController::class)->name('clients-search.index');

    Route::get('projects-search', ProjectSearchController::class)->name('projects-search.index');

    Route::get('running-timer-session', [RunningTimerSessionController::class, 'show'])->name('running-timer-session.show');
    Route::get('running-timer-session/edit', [RunningTimerSessionController::class, 'edit'])->name('running-timer-session.edit');
    Route::post('running-timer-session', [RunningTimerSessionController::class, 'store'])->name('running-timer-session.store');
    Route::post('running-timer-session/completion', \App\Http\Controllers\Turbo\TimerSessionCompletionController::class)->name('running-timer-session.completion');
    Route::put('running-timer-session', [RunningTimerSessionController::class, 'update'])->name('running-timer-session.update');
    Route::delete('running-timer-session', [RunningTimerSessionController::class, 'destroy'])->name('running-timer-session.destroy');
});
