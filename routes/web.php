<?php

use App\Http\Controllers\ParseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ParseController::class, 'index']);
Route::get('/parse', [ParseController::class, 'parse']);
Route::get('/crawl', [ParseController::class, 'crawl']);
