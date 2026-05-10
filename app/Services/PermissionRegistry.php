<?php

namespace App\Services;

/**
 * Centralized registry for all RBAC permissions and role definitions.
 * Single source of truth — no magic strings scattered across the codebase.
 *
 * Architecture:
 *   - Super Admin  = System management ONLY (pengaturan, user, role, log)
 *   - Admin        = Full operational access (anggota, simpanan, pinjaman, dll.)
 *   - Pimpinan     = Executive viewer (read-only + approve pinjaman)
 *   - Custom Roles = Flexible, cannot access system write permissions
 */
class PermissionRegistry
{
    // ============================
    // Core Role Slugs
    // ============================

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN       = 'admin';
    public const ROLE_PIMPINAN    = 'pimpinan';

    /**
     * Roles that CANNOT be deleted or renamed from the UI.
     */
    public const PROTECTED_ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_PIMPINAN,
    ];

    // ============================
    // Permission Groups (Modules)
    // ============================

    /**
     * All system permissions, grouped by module.
     */
    public static function allPermissions(): array
    {
        return [
            'Anggota' => [
                'anggota.view'        => 'Lihat daftar anggota',
                'anggota.profile'     => 'Lihat profil detail anggota',
                'anggota.create'      => 'Tambah anggota baru',
                'anggota.edit'        => 'Edit data anggota',
                'anggota.keluar'      => 'Proses pengeluaran anggota',
                'anggota.reaktivasi'  => 'Reaktivasi anggota nonaktif',
            ],

            'Simpanan' => [
                'simpanan.view'    => 'Lihat buku simpanan anggota',
                'simpanan.create'  => 'Catat setoran simpanan',
            ],

            'Pinjaman' => [
                'pinjaman.view'    => 'Lihat daftar pengajuan pinjaman',
                'pinjaman.aktif'   => 'Lihat daftar pinjaman aktif',
                'pinjaman.detail'  => 'Lihat detail/rincian pengajuan pinjaman',
                'pinjaman.approve' => 'Approve pengajuan pinjaman',
                'pinjaman.reject'  => 'Reject pengajuan pinjaman',
                'pinjaman.bayar'   => 'Catat pembayaran angsuran',
            ],

            'Periode' => [
                'periode.view'   => 'Lihat daftar periode pinjaman',
                'periode.create' => 'Buka periode baru',
                'periode.edit'   => 'Edit konfigurasi periode',
                'periode.delete' => 'Hapus/tutup periode',
            ],

            'Pengeluaran' => [
                'pengeluaran.view'   => 'Lihat kas operasional',
                'pengeluaran.create' => 'Catat pengeluaran baru',
                'pengeluaran.delete' => 'Hapus catatan pengeluaran',
            ],

            'Potongan' => [
                'potongan.view'   => 'Lihat potongan TPP',
                'potongan.proses' => 'Proses potongan bulanan',
            ],

            'Laporan' => [
                'laporan.ringkasan' => 'Lihat ringkasan laporan',
                'laporan.neraca'    => 'Lihat neraca keuangan',
                'laporan.arsip'     => 'Lihat daftar arsip laporan',
                'laporan.arsip_manage' => 'Pin, unpin, hapus arsip laporan',
            ],

            'Simulasi' => [
                'simulasi.aliran_dana' => 'Lihat proyeksi aliran dana',
                'simulasi.shu'         => 'Lihat simulasi pembagian SHU',
            ],

            'SHU' => [
                'shu.manage' => 'Kelola komponen, distribusi, & payout SHU',
            ],

            'Pengaturan' => [
                'pengaturan.view' => 'Lihat halaman pengaturan sistem',
                'pengaturan.edit' => 'Ubah konfigurasi sistem',
            ],

            'User' => [
                'user.view'       => 'Lihat daftar pengguna',
                'user.create'     => 'Tambah pengguna baru',
                'user.edit'       => 'Edit data pengguna',
                'user.deactivate' => 'Nonaktifkan/aktifkan pengguna',
            ],

            'Role' => [
                'role.view'   => 'Lihat daftar role & permission',
                'role.create' => 'Tambah role baru',
                'role.edit'   => 'Edit role & assign permission',
                'role.delete' => 'Hapus role',
            ],

            'Log' => [
                'log.view' => 'Lihat log aktivitas',
            ],

            'Pembatalan Transaksi' => [
                'void.view'    => 'Lihat riwayat pembatalan transaksi',
                'void.request' => 'Ajukan permintaan pembatalan transaksi',
                'void.approve' => 'Setujui/tolak pembatalan transaksi',
            ],
        ];
    }

    /**
     * Flat list of all permission names.
     */
    public static function allPermissionNames(): array
    {
        return collect(static::allPermissions())
            ->flatMap(fn(array $perms) => array_keys($perms))
            ->values()
            ->all();
    }

    /**
     * Permission implications: having the key permission
     * automatically grants the value permissions.
     *
     * Example: anggota.profile → auto-grants anggota.view
     */
    public static function permissionImplications(): array
    {
        return [
            'anggota.profile' => ['anggota.view'],
            'pinjaman.detail' => ['pinjaman.view'],
        ];
    }

    /**
     * Permissions EXCLUSIVE to Super Admin.
     * These CANNOT be assigned to any other role via UI.
     * Other roles can only have the .view variants.
     */
    public static function superAdminOnlyPermissions(): array
    {
        return [
            'pengaturan.edit',
            'user.create',
            'user.edit',
            'user.deactivate',
            'role.create',
            'role.edit',
            'role.delete',
        ];
    }

    /**
     * Permissions that can be assigned to non-Super-Admin roles via the UI.
     * Excludes all super-admin-only write permissions.
     */
    public static function assignablePermissions(): array
    {
        $excluded = static::superAdminOnlyPermissions();

        return collect(static::allPermissions())
            ->map(fn(array $perms) => collect($perms)
                ->reject(fn($label, $name) => in_array($name, $excluded))
                ->all())
            ->filter(fn(array $perms) => !empty($perms))
            ->all();
    }

    /**
     * Super Admin permissions: ONLY system management.
     * SA manages the platform — not the koperasi operations.
     */
    public static function superAdminPermissions(): array
    {
        return [
            'pengaturan.view',
            'pengaturan.edit',
            'user.view',
            'user.create',
            'user.edit',
            'user.deactivate',
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'log.view',
            'laporan.arsip',
            'laporan.arsip_manage',
        ];
    }

    /**
     * Admin/Pengurus: full operational access + view-only for system.
     */
    public static function adminPermissions(): array
    {
        return [
            // Anggota (full)
            'anggota.view', 'anggota.profile', 'anggota.create', 'anggota.edit',
            'anggota.keluar', 'anggota.reaktivasi',
            // Simpanan
            'simpanan.view', 'simpanan.create',
            // Pinjaman
            'pinjaman.view', 'pinjaman.aktif', 'pinjaman.detail', 'pinjaman.approve', 'pinjaman.reject', 'pinjaman.bayar',
            // Periode
            'periode.view', 'periode.create', 'periode.edit', 'periode.delete',
            // Pengeluaran
            'pengeluaran.view', 'pengeluaran.create', 'pengeluaran.delete',
            // Potongan
            'potongan.view', 'potongan.proses',
            // Laporan & Simulasi
            'laporan.ringkasan', 'laporan.neraca', 'laporan.arsip', 'laporan.arsip_manage',
            'simulasi.aliran_dana', 'simulasi.shu',
            // SHU
            'shu.manage',
            // System (view-only)
            'pengaturan.view', 'user.view', 'role.view',
            // Log & Arsip
            'log.view',
            // Void (request only — approve is Pimpinan)
            'void.view', 'void.request',
        ];
    }

    /**
     * Pimpinan/Kepala: executive viewer + approve pinjaman.
     */
    public static function pimpinanPermissions(): array
    {
        return [
            'anggota.view',
            'anggota.profile',
            'simpanan.view',
            'pinjaman.view',
            'pinjaman.aktif',
            'pinjaman.detail',
            'pinjaman.approve',
            'pinjaman.reject',
            'periode.view',
            'pengeluaran.view',
            'potongan.view',
            'laporan.ringkasan',
            'laporan.neraca',
            'laporan.arsip',
            'simulasi.aliran_dana',
            'simulasi.shu',
            // System (view-only)
            'pengaturan.view', 'user.view', 'role.view',
            // Log & Arsip
            'log.view',
            // Void (approve/reject)
            'void.view', 'void.approve',
        ];
    }

    /**
     * Get human-readable label for a role slug.
     */
    public static function roleLabel(string $slug): string
    {
        return match ($slug) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN       => 'Admin / Pengurus',
            self::ROLE_PIMPINAN    => 'Pimpinan / Kepala',
            default                => ucwords(str_replace('_', ' ', $slug)),
        };
    }

    /**
     * Get human-readable label for a permission slug.
     */
    public static function permissionLabel(string $slug): string
    {
        foreach (static::allPermissions() as $group => $perms) {
            if (isset($perms[$slug])) {
                return $perms[$slug];
            }
        }
        return $slug;
    }
}
