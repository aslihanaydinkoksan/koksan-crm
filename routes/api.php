<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DynamicFieldController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\ContactPersonController;

// Form Şema Endpoint'i
Route::apiResource('dynamic-fields', DynamicFieldController::class)->only(['index', 'store', 'update', 'destroy']);

// Müşteri CRUD Endpoint'leri (index, store, update, destroy)
Route::apiResource('customers', CustomerController::class);

// Person listeleme (Select box için)
Route::get('/people', [PersonController::class, 'index']);

// Numune CRUD Endpoint'leri (index, store, update, destroy)
Route::apiResource('samples', SampleController::class);

//Fırsatlar ve duyumlar için endpoint'ler
Route::apiResource('opportunities', OpportunityController::class);

// Ziyaret CRUD Endpoint'leri (index, store, update, destroy)
Route::apiResource('visits', VisitController::class);

// Makineler için endpoint'ler 
Route::apiResource('machines', MachineController::class);

// Laboratuvar Testleri için endpoint'ler
Route::apiResource('lab-tests', LabTestController::class);

// İletişim Kişileri için özel endpoint'ler
Route::apiResource('contacts', ContactPersonController::class)->only(['index', 'store']);