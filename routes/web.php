<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SummarizeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::post('/summarize', [SummarizeController::class, 'summarizeDocument'])->name('summarizeDocument');
