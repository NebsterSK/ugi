<?php

use App\Http\Controllers\EntriesController;
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
        Route::get('/{entry}/toggleFavorite', [EntriesController::class, 'toggleFavorite'])->name('favorite');
        Route::get('/{entry}/toggleIgnore', [EntriesController::class, 'toggleIgnore'])->name('ignore');
    });
});

Auth::routes();
