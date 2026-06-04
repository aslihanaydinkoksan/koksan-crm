<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DynamicFieldController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SampleController;

// Form Şema Endpoint'i
Route::get('/dynamic-fields', [DynamicFieldController::class, 'index']);

// Müşteri CRUD Endpoint'leri (index, store, update, destroy)
Route::apiResource('customers', CustomerController::class);

// Person listeleme (Select box için)
Route::get('/people', [PersonController::class, 'index']);

// Numune CRUD Endpoint'leri (index, store, update, destroy)
Route::apiResource('samples', SampleController::class);