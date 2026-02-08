<?php

use App\Http\Controllers\SummarizeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/summarize', [SummarizeController::class, 'summarizeDocument'])->name('summarizeDocument');
