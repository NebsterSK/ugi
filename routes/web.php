<?php

use App\Http\Controllers\ParseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::middleware('auth')->group(function () {
    Route::prefix('entries')->name('entries.')->group(function () {
        Route::get('/', [ParseController::class, 'index'])->name('index');
        Route::get('/{entry}', [ParseController::class, 'show'])->name('show');
        Route::put('/{entry}', [ParseController::class, 'update'])->name('update');
        Route::get('/{entry}/toggleFavorite', [ParseController::class, 'favorite'])->name('favorite');
        Route::get('/{entry}/toggleIgnore', [ParseController::class, 'ignore'])->name('ignore');
    });
});

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();
