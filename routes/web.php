<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerWebController;
use App\Http\Controllers\SampleWebController;

// Test Arayüzü Rotası
Route::get('/customers', [CustomerWebController::class, 'index']);

// Numune test arayüzü rotası
Route::get('/samples', [SampleWebController::class, 'index']);