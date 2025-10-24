<?php

use App\Http\Controllers\ParseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ParseController::class, 'index'])->name('index');
Route::get('/{entry}', [ParseController::class, 'show'])->name('show');
Route::get('/{entry}/toggleFavorite', [ParseController::class, 'favorite'])->name('favorite');
Route::get('/parse', [ParseController::class, 'parse'])->name('parse');
Route::get('/crawl', [ParseController::class, 'crawl'])->name('crawl');
