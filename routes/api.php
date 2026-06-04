<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DynamicFieldController;

// Form Şema Endpoint'i
Route::get('/dynamic-fields', [DynamicFieldController::class, 'index']);

// Müşteri CRUD Endpoint'leri (index, store, update, destroy)
Route::apiResource('customers', CustomerController::class);