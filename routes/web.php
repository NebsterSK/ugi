<?php

use App\Http\Controllers\ParseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ParseController::class, 'index'])->name('index');
Route::get('/{entry}', [ParseController::class, 'show'])->name('show');
Route::put('/{entry}', [ParseController::class, 'update'])->name('update');
Route::get('/{entry}/toggleFavorite', [ParseController::class, 'favorite'])->name('favorite');
Route::get('/{entry}/toggleIgnore', [ParseController::class, 'ignore'])->name('ignore');
Route::get('/parse', [ParseController::class, 'parse'])->name('parse');
Route::get('/crawl', [ParseController::class, 'crawl'])->name('crawl');
