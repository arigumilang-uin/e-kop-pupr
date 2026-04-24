<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Keuangan\SimpananController;
use App\Http\Controllers\Keuangan\PotonganBulananController;
use App\Http\Controllers\Keuangan\LaporanKeuanganController;
use App\Http\Controllers\Keuangan\SimulasiKeuanganController;
use App\Http\Controllers\Keuangan\ArsipTransaksiController;
use App\Http\Controllers\Master\AnggotaController;
use App\Http\Controllers\Periode\PeriodeController;
use App\Http\Controllers\Pinjaman\PinjamanAdminController;
use App\Http\Controllers\Pinjaman\PinjamanGuestController;
use App\Http\Controllers\Simulasi\SimulasiController;
use App\Http\Controllers\Sistem\PengaturanController;
use App\Http\Controllers\Sistem\LogAktivitasController;
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
    Route::resource('anggota', AnggotaController::class);

    // === Keuangan ===
    Route::resource('simpanan', SimpananController::class)->except(['show', 'edit', 'update', 'destroy']);
    Route::get('/potongan', [PotonganBulananController::class, 'index'])->name('potongan.index');
    Route::post('/potongan/proses', [PotonganBulananController::class, 'proses'])->name('potongan.proses');
    Route::get('/keuangan/laporan', [LaporanKeuanganController::class, 'index'])->name('keuangan.laporan');
    Route::get('/keuangan/simulasi', [SimulasiKeuanganController::class, 'index'])->name('keuangan.simulasi');
    Route::get('/keuangan/arsip', [ArsipTransaksiController::class, 'index'])->name('keuangan.arsip');

    // === Pinjaman Admin/Approve ===
    Route::get('/pinjaman', [PinjamanAdminController::class, 'index'])->name('pinjaman.index');
    Route::get('/pinjaman/{pinjaman}', [PinjamanAdminController::class, 'show'])->name('pinjaman.show');
    Route::patch('/pinjaman/{pinjaman}/approve', [PinjamanAdminController::class, 'approve'])->name('pinjaman.approve');
    Route::patch('/pinjaman/{pinjaman}/reject', [PinjamanAdminController::class, 'reject'])->name('pinjaman.reject');
    Route::patch('/pinjaman/{pinjaman}/angsuran/{angsuran}/bayar', [PinjamanAdminController::class, 'bayarAngsuran'])->name('pinjaman.angsuran.bayar');

    // === Periode Pinjaman ===
    Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
    Route::get('/periode/create', [PeriodeController::class, 'create'])->name('periode.create');
    Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
    Route::get('/periode/{periode}', [PeriodeController::class, 'show'])->name('periode.show');
    Route::patch('/periode/{periode}/tutup', [PeriodeController::class, 'tutup'])->name('periode.tutup');
    Route::patch('/periode/{periode}/buka', [PeriodeController::class, 'buka'])->name('periode.buka');
    Route::patch('/periode/{periode}/reset-token', [PeriodeController::class, 'resetToken'])->name('periode.reset-token');

    // === Sistem ===
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::patch('/pengaturan/{pengaturan}', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::get('/log', [LogAktivitasController::class, 'index'])->name('log.index');
});

/*
|--------------------------------------------------------------------------
| Guest Routes — Pinjaman (Via Link Token, tanpa login)
|--------------------------------------------------------------------------
*/

Route::get('/pinjaman/ajukan/{token}', [PinjamanGuestController::class, 'form'])->name('pinjaman.guest.form');
Route::post('/pinjaman/ajukan/{token}', [PinjamanGuestController::class, 'submit'])->name('pinjaman.guest.submit');
Route::get('/cek-pinjaman', [PinjamanGuestController::class, 'statusForm'])->name('pinjaman.guest.status');
Route::post('/cek-pinjaman', [PinjamanGuestController::class, 'statusCheck'])->name('pinjaman.guest.check');
Route::patch('/cek-pinjaman/batal', [PinjamanGuestController::class, 'cancel'])->name('pinjaman.guest.cancel');
