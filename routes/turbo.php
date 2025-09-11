<?php

use App\Http\Controllers\Turbo\ClientController;
use App\Http\Controllers\Turbo\ClientSearchController;
use App\Http\Controllers\Turbo\ProjectController;
use App\Http\Controllers\Turbo\ProjectSearchController;
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
    Route::post('clients-search', [ClientSearchController::class, 'store'])->name('clients-search.store');

    Route::get('projects-search', [ProjectSearchController::class, 'index'])->name('projects-search.index');
    Route::post('projects-search', [ProjectSearchController::class, 'store'])->name('projects-search.store');
});
