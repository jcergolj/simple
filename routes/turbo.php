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
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');

    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');

    Route::get('time-entries/create', [TimeEntryController::class, 'create'])->name('time-entries.create');
    Route::get('time-entries/{time_entry}/edit', [TimeEntryController::class, 'edit'])->name('time-entries.edit');

    Route::get('clients-search', [ClientSearchController::class, 'index'])->name('clients-search.index');

    Route::get('projects-search', [ProjectSearchController::class, 'index'])->name('projects-search.index');

    Route::get('running-timer-session', [RunningTimerSessionController::class, 'show'])->name('running-timer-session.show');
    Route::get('running-timer-session/edit', [RunningTimerSessionController::class, 'edit'])->name('running-timer-session.edit');
    Route::post('running-timer-session', [RunningTimerSessionController::class, 'store'])->name('running-timer-session.store');
    Route::patch('running-timer-session/stop', [RunningTimerSessionController::class, 'stop'])->name('running-timer-session.stop');
    Route::put('running-timer-session', [RunningTimerSessionController::class, 'update'])->name('running-timer-session.update');
    Route::delete('running-timer-session', [RunningTimerSessionController::class, 'destroy'])->name('running-timer-session.destroy');
});
