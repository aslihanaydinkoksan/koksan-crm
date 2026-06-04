<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerWebController;
use App\Http\Controllers\SampleWebController;

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
    Route::get('/samples', [SampleWebController::class, 'index'])->name('samples.index');
});
