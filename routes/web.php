<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerWebController;
use App\Http\Controllers\SampleWebController;
use App\Http\Controllers\OpportunityWebController;
use App\Http\Controllers\VisitWebController;
use App\Http\Controllers\MachineWebController;
use App\Http\Controllers\LabTestWebController;
use App\Http\Controllers\SettingsWebController;

// --- AUTH ROTALARI ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- KORUMALI ROTALAR (Sadece Giriş Yapanlar) ---
Route::middleware('auth')->group(function () {

    // Ana sayfa yönlendirmesi
    Route::get('/', function () {
        return redirect('/customers');
    });

    // Operasyonel Test Rotalarımız
    Route::get('/customers', [CustomerWebController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerWebController::class, 'show'])->name('customers.show');
    Route::get('/samples', [SampleWebController::class, 'index'])->name('samples.index');
    Route::get('/opportunities', [OpportunityWebController::class, 'index'])->name('opportunities.index');
    Route::get('/visits', [VisitWebController::class, 'index'])->name('visits.index');
    Route::get('/machines', [MachineWebController::class, 'index'])->name('machines.index');
    Route::get('/lab-tests', [LabTestWebController::class, 'index'])->name('lab-tests.index');
    Route::get('/settings/dynamic-fields', [SettingsWebController::class, 'dynamicFields'])->name('settings.dynamic-fields');
});
