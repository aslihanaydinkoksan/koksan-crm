<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerWebController;

// Test Arayüzü Rotası
Route::get('/customers', [CustomerWebController::class, 'index']);