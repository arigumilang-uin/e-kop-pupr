<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Keuangan\SimpananController;
use App\Http\Controllers\Master\AnggotaController;
use App\Http\Controllers\Periode\PeriodeController;
use App\Http\Controllers\Pinjaman\PinjamanGuestController;
use App\Http\Controllers\Simulasi\SimulasiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Tanpa Login)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/simulasi', [SimulasiController::class, 'index'])->name('simulasi');
Route::post('/simulasi/hitung', [SimulasiController::class, 'hitung'])->name('simulasi.hitung');

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest Only — belum login)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Admin & Pimpinan)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === Master Data ===
    Route::resource('anggota', AnggotaController::class)->except(['show']);

    // === Keuangan ===
    Route::resource('simpanan', SimpananController::class)->except(['show', 'edit', 'update', 'destroy']);

    // === Periode Pinjaman ===
    Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
    Route::get('/periode/create', [PeriodeController::class, 'create'])->name('periode.create');
    Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
    Route::get('/periode/{periode}', [PeriodeController::class, 'show'])->name('periode.show');
    Route::patch('/periode/{periode}/tutup', [PeriodeController::class, 'tutup'])->name('periode.tutup');
    Route::patch('/periode/{periode}/buka', [PeriodeController::class, 'buka'])->name('periode.buka');
    Route::patch('/periode/{periode}/reset-token', [PeriodeController::class, 'resetToken'])->name('periode.reset-token');
});

/*
|--------------------------------------------------------------------------
| Guest Routes — Pinjaman (Via Link Token, tanpa login)
|--------------------------------------------------------------------------
*/

Route::get('/pinjaman/ajukan/{token}', [PinjamanGuestController::class, 'form'])->name('pinjaman.guest.form');
Route::post('/pinjaman/ajukan/{token}', [PinjamanGuestController::class, 'submit'])->name('pinjaman.guest.submit');
Route::get('/pinjaman/status', [PinjamanGuestController::class, 'statusForm'])->name('pinjaman.guest.status');
Route::post('/pinjaman/status', [PinjamanGuestController::class, 'statusCheck'])->name('pinjaman.guest.check');
