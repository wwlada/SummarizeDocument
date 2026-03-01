<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SummarizeController;
use App\Http\Middleware\SummaryAccess;
use App\Http\Middleware\TrackVisitor;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->middleware(TrackVisitor::class);

Route::post('/summarize', [SummarizeController::class, 'summarizeDocument'])
    ->name('summarizeDocument')
    ->middleware([SummaryAccess::class, 'throttle:6,1']);
