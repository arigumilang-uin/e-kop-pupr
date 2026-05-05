<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Keuangan\SimpananController;
use App\Http\Controllers\Keuangan\PotonganBulananController;
use App\Http\Controllers\Keuangan\LaporanKeuanganController;
use App\Http\Controllers\Keuangan\SimulasiKeuanganController;
use App\Http\Controllers\Keuangan\ArsipTransaksiController;
use App\Http\Controllers\Keuangan\PengeluaranKasController;
use App\Http\Controllers\Keuangan\ShuController;
use App\Http\Controllers\Keuangan\NeracaController;
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
| Guest Routes — Pinjaman (Via Link Token, tanpa login)
| PENTING: Harus didaftarkan SEBELUM auth routes karena
| /pinjaman/{pinjaman} wildcard di auth group akan menangkap /pinjaman/ajukan
|--------------------------------------------------------------------------
*/

Route::get('/pinjaman/ajukan', [PinjamanGuestController::class, 'formRedirect'])->name('pinjaman.guest.form_redirect');
Route::get('/pinjaman/ajukan/{token}', [PinjamanGuestController::class, 'form'])->name('pinjaman.guest.form');
Route::post('/pinjaman/ajukan/{token}/review', [PinjamanGuestController::class, 'review'])->name('pinjaman.guest.review');
Route::post('/pinjaman/ajukan/{token}/store', [PinjamanGuestController::class, 'store'])->name('pinjaman.guest.store');
Route::get('/cek-pinjaman', [PinjamanGuestController::class, 'statusForm'])->name('pinjaman.guest.status');
Route::post('/cek-pinjaman', [PinjamanGuestController::class, 'statusCheck'])->name('pinjaman.guest.check');
Route::patch('/cek-pinjaman/batal', [PinjamanGuestController::class, 'cancel'])->name('pinjaman.guest.cancel');

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
    Route::get('/dashboard/simpanan-data', [DashboardController::class, 'simpananData'])->name('dashboard.simpanan-data');

    // =============================================
    // WRITE — Admin Only (HARUS didaftarkan SEBELUM
    // read-only group karena ada wildcard {anggotum})
    // =============================================
    Route::middleware('role:admin')->group(function () {
        // Master Anggota (CRUD)
        Route::get('/anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
        Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
        Route::get('/anggota/{anggotum}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
        Route::put('/anggota/{anggotum}', [AnggotaController::class, 'update'])->name('anggota.update');
        Route::delete('/anggota/{anggotum}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
        Route::get('/anggota/{anggota}/keluar', [AnggotaController::class, 'keluarAnalisis'])->name('anggota.keluar');
        Route::post('/anggota/{anggota}/keluar', [AnggotaController::class, 'keluarProses'])->name('anggota.keluar.proses');
        Route::get('/anggota/{anggota}/reaktivasi', [AnggotaController::class, 'reaktivasiForm'])->name('anggota.reaktivasi');
        Route::post('/anggota/{anggota}/reaktivasi', [AnggotaController::class, 'reaktivasiProses'])->name('anggota.reaktivasi.proses');

        // Simpanan (Create)
        Route::get('/simpanan/create', [SimpananController::class, 'create'])->name('simpanan.create');
        Route::post('/simpanan', [SimpananController::class, 'store'])->name('simpanan.store');

        // Potongan TPP (Proses)
        Route::post('/potongan/proses', [PotonganBulananController::class, 'proses'])->name('potongan.proses');

        // Pinjaman (Approve/Reject/Bayar)
        Route::get('/pinjaman/aktif', [PinjamanAdminController::class, 'aktif'])->name('pinjaman.aktif');
        Route::post('/pinjaman/mass-approve', [PinjamanAdminController::class, 'massApprove'])->name('pinjaman.massApprove');
        Route::post('/pinjaman/mass-reject', [PinjamanAdminController::class, 'massReject'])->name('pinjaman.massReject');
        Route::patch('/pinjaman/{pinjaman}/approve', [PinjamanAdminController::class, 'approve'])->name('pinjaman.approve');
        Route::patch('/pinjaman/{pinjaman}/reject', [PinjamanAdminController::class, 'reject'])->name('pinjaman.reject');
        Route::patch('/pinjaman/{pinjaman}/angsuran/{angsuran}/bayar', [PinjamanAdminController::class, 'bayarAngsuran'])->name('pinjaman.angsuran.bayar');

        // Pengeluaran Kas (CRUD)
        Route::post('/pengeluaran/kategori', [PengeluaranKasController::class, 'storeKategori'])->name('pengeluaran.kategori.store');
        Route::post('/pengeluaran', [PengeluaranKasController::class, 'store'])->name('pengeluaran.store');
        Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranKasController::class, 'destroy'])->name('pengeluaran.destroy');

        // SHU Konfigurasi (CRUD)
        Route::post('/keuangan/shu/komponen', [ShuController::class, 'storeKomponen'])->name('shu.komponen.store');
        Route::patch('/keuangan/shu/komponen/{komponen}', [ShuController::class, 'updateKomponen'])->name('shu.komponen.update');
        Route::delete('/keuangan/shu/komponen/{komponen}', [ShuController::class, 'destroyKomponen'])->name('shu.komponen.destroy');
        Route::post('/keuangan/shu/distribusi', [ShuController::class, 'storeDistribusi'])->name('shu.distribusi.store');
        Route::patch('/keuangan/shu/distribusi/{distribusi}', [ShuController::class, 'updateDistribusi'])->name('shu.distribusi.update');
        Route::delete('/keuangan/shu/distribusi/{distribusi}', [ShuController::class, 'destroyDistribusi'])->name('shu.distribusi.destroy');
        Route::post('/keuangan/shu/payout', [ShuController::class, 'eksekusiPayout'])->name('shu.payout');

        // Periode Pinjaman (CRUD)
        Route::get('/periode/create', [PeriodeController::class, 'create'])->name('periode.create');
        Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
        Route::get('/periode/{periode}/edit', [PeriodeController::class, 'edit'])->name('periode.edit');
        Route::put('/periode/{periode}', [PeriodeController::class, 'update'])->name('periode.update');
        Route::delete('/periode/{periode}', [PeriodeController::class, 'destroy'])->name('periode.destroy');
        Route::patch('/periode/{periode}/tutup', [PeriodeController::class, 'tutup'])->name('periode.tutup');
        Route::patch('/periode/{periode}/buka', [PeriodeController::class, 'buka'])->name('periode.buka');
        Route::patch('/periode/{periode}/reset-token', [PeriodeController::class, 'resetToken'])->name('periode.reset-token');

        // Pengaturan Sistem (Update)
        Route::patch('/pengaturan/{pengaturan}', [PengaturanController::class, 'update'])->name('pengaturan.update');
    });

    // =============================================
    // READ-ONLY — Admin & Pimpinan
    // (Wildcard routes SETELAH specific routes)
    // =============================================
    Route::middleware('role:admin,pimpinan')->group(function () {
        // Laporan & Monitor
        Route::get('/keuangan/laporan', [LaporanKeuanganController::class, 'index'])->name('keuangan.laporan');
        Route::get('/keuangan/simulasi', [SimulasiKeuanganController::class, 'index'])->name('keuangan.simulasi');
        Route::get('/keuangan/shu', [ShuController::class, 'index'])->name('keuangan.shu');
        Route::get('/keuangan/neraca', [NeracaController::class, 'index'])->name('keuangan.neraca');
        Route::get('/keuangan/arsip', [ArsipTransaksiController::class, 'index'])->name('keuangan.arsip');
        Route::get('/log', [LogAktivitasController::class, 'index'])->name('log.index');

        // View anggota & pinjaman (read-only)
        Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
        Route::get('/anggota/{anggotum}', [AnggotaController::class, 'show'])->name('anggota.show');
        Route::get('/pinjaman', [PinjamanAdminController::class, 'index'])->name('pinjaman.index');
        Route::get('/pinjaman/{pinjaman}', [PinjamanAdminController::class, 'show'])->name('pinjaman.show');
        Route::get('/simpanan', [SimpananController::class, 'index'])->name('simpanan.index');
        Route::get('/simpanan/riwayat', [SimpananController::class, 'riwayat'])->name('simpanan.riwayat');
        Route::get('/simpanan/export/excel', [SimpananController::class, 'exportExcel'])->name('simpanan.export.excel');
        Route::get('/simpanan/export/pdf', [SimpananController::class, 'exportPdf'])->name('simpanan.export.pdf');
        Route::get('/pengeluaran', [PengeluaranKasController::class, 'index'])->name('pengeluaran.index');
        Route::get('/potongan', [PotonganBulananController::class, 'index'])->name('potongan.index');
        Route::get('/potongan/export/excel', [PotonganBulananController::class, 'exportExcel'])->name('potongan.export.excel');
        Route::get('/potongan/export/pdf', [PotonganBulananController::class, 'exportPdf'])->name('potongan.export.pdf');
        Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::get('/periode/{periode}', [PeriodeController::class, 'show'])->name('periode.show');
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    });
});
