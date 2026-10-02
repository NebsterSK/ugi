<?php

use App\Http\Controllers\EntriesController;
use App\Http\Controllers\FiltersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::middleware('auth')->group(function () {
    Route::prefix('entries')->name('entries.')->group(function () {
        Route::get('/', [EntriesController::class, 'index'])->name('index');
        Route::get('/favorite', [EntriesController::class, 'favorite'])->name('favorite');
        Route::get('/seen', [EntriesController::class, 'seen'])->name('seen');
        Route::get('/{entry}', [EntriesController::class, 'show'])->name('show');
        Route::put('/{entry}', [EntriesController::class, 'update'])->name('update');
        Route::get('/{entry}/toggleFavorite', [EntriesController::class, 'toggleFavorite'])->name('toggleFavorite');
        Route::get('/{entry}/toggleIgnore', [EntriesController::class, 'toggleIgnore'])->name('toggleIgnore');
    });

    Route::prefix('filters')->name('filters.')->group(function () {
        Route::get('/', [FiltersController::class, 'index'])->name('index');
        Route::get('/create', [FiltersController::class, 'create'])->name('create');
        Route::post('/', [FiltersController::class, 'store'])->name('store');
        Route::get('/{filter}/edit', [FiltersController::class, 'edit'])->name('edit');
        Route::put('/{filter}', [FiltersController::class, 'update'])->name('update');
        Route::delete('/{filter}', [FiltersController::class, 'destroy'])->name('destroy');
        Route::get('/{filter}/toggleActive', [FiltersController::class, 'toggleActive'])->name('toggleActive');
    });
});

Auth::routes();
