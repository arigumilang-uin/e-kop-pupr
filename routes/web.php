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
use App\Http\Controllers\Sistem\RoleController;
use App\Http\Controllers\UserController;
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
| Authenticated Routes — Protected by Spatie Permission Middleware
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/simpanan-data', [DashboardController::class, 'simpananData'])->name('dashboard.simpanan-data');

    // =============================================
    // ANGGOTA
    // =============================================
    Route::middleware('permission:anggota.view')->group(function () {
        Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    });

    Route::get('/anggota/{anggotum}', [AnggotaController::class, 'show'])
        ->name('anggota.show')
        ->middleware('permission:anggota.profile');

    Route::middleware('permission:anggota.create')->group(function () {
        Route::get('/anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
        Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    });

    Route::middleware('permission:anggota.edit')->group(function () {
        Route::get('/anggota/{anggotum}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
        Route::put('/anggota/{anggotum}', [AnggotaController::class, 'update'])->name('anggota.update');
        Route::delete('/anggota/{anggotum}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
    });

    Route::middleware('permission:anggota.keluar')->group(function () {
        Route::get('/anggota/{anggota}/keluar', [AnggotaController::class, 'keluarAnalisis'])->name('anggota.keluar');
        Route::post('/anggota/{anggota}/keluar', [AnggotaController::class, 'keluarProses'])->name('anggota.keluar.proses');
    });

    Route::middleware('permission:anggota.reaktivasi')->group(function () {
        Route::get('/anggota/{anggota}/reaktivasi', [AnggotaController::class, 'reaktivasiForm'])->name('anggota.reaktivasi');
        Route::post('/anggota/{anggota}/reaktivasi', [AnggotaController::class, 'reaktivasiProses'])->name('anggota.reaktivasi.proses');
    });

    // =============================================
    // SIMPANAN
    // =============================================
    Route::middleware('permission:simpanan.view')->group(function () {
        Route::get('/simpanan', [SimpananController::class, 'index'])->name('simpanan.index');
        Route::get('/simpanan/export/excel', [SimpananController::class, 'exportExcel'])->name('simpanan.export.excel');
        Route::get('/simpanan/export/pdf', [SimpananController::class, 'exportPdf'])->name('simpanan.export.pdf');
    });

    Route::get('/simpanan/riwayat', [SimpananController::class, 'riwayat'])
        ->name('simpanan.riwayat')
        ->middleware('permission:simpanan.riwayat');

    Route::middleware('permission:simpanan.create')->group(function () {
        Route::get('/simpanan/create', [SimpananController::class, 'create'])->name('simpanan.create');
        Route::post('/simpanan', [SimpananController::class, 'store'])->name('simpanan.store');
    });

    // =============================================
    // POTONGAN TPP
    // =============================================
    Route::get('/potongan', [PotonganBulananController::class, 'index'])->name('potongan.index')->middleware('permission:potongan.view');
    Route::get('/potongan/export/excel', [PotonganBulananController::class, 'exportExcel'])->name('potongan.export.excel')->middleware('permission:potongan.view');
    Route::get('/potongan/export/pdf', [PotonganBulananController::class, 'exportPdf'])->name('potongan.export.pdf')->middleware('permission:potongan.view');
    Route::post('/potongan/proses', [PotonganBulananController::class, 'proses'])->name('potongan.proses')->middleware('permission:potongan.proses');

    // =============================================
    // PINJAMAN
    // =============================================
    Route::middleware('permission:pinjaman.view')->group(function () {
        Route::get('/pinjaman', [PinjamanAdminController::class, 'index'])->name('pinjaman.index');
    });

    Route::get('/pinjaman/aktif', [PinjamanAdminController::class, 'aktif'])
        ->name('pinjaman.aktif')
        ->middleware('permission:pinjaman.aktif');

    Route::get('/pinjaman/{pinjaman}', [PinjamanAdminController::class, 'show'])
        ->name('pinjaman.show')
        ->middleware('permission:pinjaman.detail');

    Route::middleware('permission:pinjaman.approve')->group(function () {
        Route::patch('/pinjaman/{pinjaman}/approve', [PinjamanAdminController::class, 'approve'])->name('pinjaman.approve');
        Route::post('/pinjaman/mass-approve', [PinjamanAdminController::class, 'massApprove'])->name('pinjaman.massApprove');
    });

    Route::middleware('permission:pinjaman.reject')->group(function () {
        Route::patch('/pinjaman/{pinjaman}/reject', [PinjamanAdminController::class, 'reject'])->name('pinjaman.reject');
        Route::post('/pinjaman/mass-reject', [PinjamanAdminController::class, 'massReject'])->name('pinjaman.massReject');
    });

    Route::patch('/pinjaman/{pinjaman}/angsuran/{angsuran}/bayar', [PinjamanAdminController::class, 'bayarAngsuran'])
        ->name('pinjaman.angsuran.bayar')
        ->middleware('permission:pinjaman.bayar');

    // =============================================
    // PENGELUARAN KAS
    // =============================================
    Route::get('/pengeluaran', [PengeluaranKasController::class, 'index'])->name('pengeluaran.index')->middleware('permission:pengeluaran.view');

    Route::middleware('permission:pengeluaran.create')->group(function () {
        Route::post('/pengeluaran/kategori', [PengeluaranKasController::class, 'storeKategori'])->name('pengeluaran.kategori.store');
        Route::post('/pengeluaran', [PengeluaranKasController::class, 'store'])->name('pengeluaran.store');
    });

    Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranKasController::class, 'destroy'])
        ->name('pengeluaran.destroy')
        ->middleware('permission:pengeluaran.delete');

    // =============================================
    // SHU
    // =============================================
    Route::get('/keuangan/shu', [ShuController::class, 'index'])->name('keuangan.shu')->middleware('permission:simulasi.view');

    Route::middleware('permission:shu.manage')->group(function () {
        Route::post('/keuangan/shu/komponen', [ShuController::class, 'storeKomponen'])->name('shu.komponen.store');
        Route::patch('/keuangan/shu/komponen/{komponen}', [ShuController::class, 'updateKomponen'])->name('shu.komponen.update');
        Route::delete('/keuangan/shu/komponen/{komponen}', [ShuController::class, 'destroyKomponen'])->name('shu.komponen.destroy');
        Route::post('/keuangan/shu/distribusi', [ShuController::class, 'storeDistribusi'])->name('shu.distribusi.store');
        Route::patch('/keuangan/shu/distribusi/{distribusi}', [ShuController::class, 'updateDistribusi'])->name('shu.distribusi.update');
        Route::delete('/keuangan/shu/distribusi/{distribusi}', [ShuController::class, 'destroyDistribusi'])->name('shu.distribusi.destroy');
        Route::post('/keuangan/shu/payout', [ShuController::class, 'eksekusiPayout'])->name('shu.payout');
    });

    // =============================================
    // PERIODE PINJAMAN
    // =============================================
    Route::middleware('permission:periode.view')->group(function () {
        Route::get('/periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::get('/periode/{periode}', [PeriodeController::class, 'show'])->name('periode.show');
    });

    Route::middleware('permission:periode.create')->group(function () {
        Route::get('/periode/create', [PeriodeController::class, 'create'])->name('periode.create');
        Route::post('/periode', [PeriodeController::class, 'store'])->name('periode.store');
    });

    Route::middleware('permission:periode.edit')->group(function () {
        Route::get('/periode/{periode}/edit', [PeriodeController::class, 'edit'])->name('periode.edit');
        Route::put('/periode/{periode}', [PeriodeController::class, 'update'])->name('periode.update');
        Route::patch('/periode/{periode}/tutup', [PeriodeController::class, 'tutup'])->name('periode.tutup');
        Route::patch('/periode/{periode}/buka', [PeriodeController::class, 'buka'])->name('periode.buka');
        Route::patch('/periode/{periode}/reset-token', [PeriodeController::class, 'resetToken'])->name('periode.reset-token');
    });

    Route::delete('/periode/{periode}', [PeriodeController::class, 'destroy'])
        ->name('periode.destroy')
        ->middleware('permission:periode.delete');

    // =============================================
    // LAPORAN & AKUNTABILITAS (Read-only)
    // =============================================
    Route::middleware('permission:laporan.view')->group(function () {
        Route::get('/keuangan/laporan', [LaporanKeuanganController::class, 'index'])->name('keuangan.laporan');
        Route::get('/keuangan/neraca', [NeracaController::class, 'index'])->name('keuangan.neraca');
        Route::get('/keuangan/arsip', [ArsipTransaksiController::class, 'index'])->name('keuangan.arsip');
    });

    // =============================================
    // SIMULASI KEUANGAN
    // =============================================
    Route::get('/keuangan/simulasi', [SimulasiKeuanganController::class, 'index'])
        ->name('keuangan.simulasi')
        ->middleware('permission:simulasi.view');

    // =============================================
    // LOG AKTIVITAS
    // =============================================
    Route::get('/log', [LogAktivitasController::class, 'index'])
        ->name('log.index')
        ->middleware('permission:log.view');

    // =============================================
    // ARSIP LAPORAN
    // =============================================
    Route::get('/arsip-laporan', [\App\Http\Controllers\Sistem\ArsipLaporanController::class, 'index'])
        ->name('arsip.index')
        ->middleware('permission:log.view'); // Use same permission as log view or settings view

    Route::get('/arsip-laporan/{arsip}/download', [\App\Http\Controllers\Sistem\ArsipLaporanController::class, 'download'])
        ->name('arsip.download')
        ->middleware('permission:log.view');

    Route::delete('/arsip-laporan/{arsip}', [\App\Http\Controllers\Sistem\ArsipLaporanController::class, 'destroy'])
        ->name('arsip.destroy')
        ->middleware('permission:pengaturan.edit'); // Only super admin can delete

    // =============================================
    // PENGATURAN SISTEM — Super Admin Only
    // =============================================
    Route::middleware('permission:pengaturan.view')->group(function () {
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    });

    Route::patch('/pengaturan/{pengaturan}', [PengaturanController::class, 'update'])
        ->name('pengaturan.update')
        ->middleware('permission:pengaturan.edit');
        
    Route::post('/pengaturan-khusus', [PengaturanController::class, 'storeKhusus'])
        ->name('pengaturan.khusus.store')
        ->middleware('permission:pengaturan.edit');
        
    Route::delete('/pengaturan-khusus/{id}', [PengaturanController::class, 'destroyKhusus'])
        ->name('pengaturan.khusus.destroy')
        ->middleware('permission:pengaturan.edit');

    // =============================================
    // MANAJEMEN USER — Super Admin Only
    // =============================================
    Route::middleware('permission:user.view')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('permission:user.create');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('permission:user.edit');

    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
        ->name('users.toggleActive')
        ->middleware('permission:user.deactivate');

    // =============================================
    // MANAJEMEN ROLE — Super Admin Only
    // =============================================
    Route::middleware('permission:role.view')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });

    Route::post('/roles', [RoleController::class, 'store'])
        ->name('roles.store')
        ->middleware('permission:role.create');

    Route::middleware('permission:role.edit')->group(function () {
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('permission:role.delete');
});
