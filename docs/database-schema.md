# Database Schema — Desain Tabel MySQL

## Deskripsi

Dokumen ini berisi desain lengkap **seluruh tabel database** untuk sistem informasi koperasi simpan pinjam Dinas PUPR Provinsi Riau.

**Prinsip sistem:**
- **Anggota TIDAK memiliki akun login** — hanya data keanggotaan untuk pencocokan
- **Hanya Admin/Pengurus dan Pimpinan/Kepala** yang memiliki akun login
- **Pengajuan pinjaman via link guest** — anggota mengisi form publik (NIP + data bank)
- **Cek status pengajuan via NIP** — anggota bisa cek tanpa login
- Bunga **15% total** (flat), potongan **5% di muka** (SWP + Dana Resiko + Biaya Admin)
- Periode peminjaman **dibuka/tutup manual** oleh pengurus, bisa berkali-kali per tahun
- **Maks 1 pinjaman aktif per tahun** (bisa override pengurus)
- **Tidak ada penarikan simpanan** selama masih anggota aktif
- Sistem **pencatatan** — semua transaksi via potongan TPP oleh pengurus

## Ringkasan Tabel

| No | Nama Tabel | Kategori | Deskripsi |
|----|------------|----------|-----------|
| 1 | `bidang` | Master | Bagian/bidang di Dinas PUPR |
| 2 | `anggota` | Master | Data anggota koperasi (untuk pencocokan, bukan login) |
| 3 | `users` | Autentikasi | Akun login — **hanya admin & pimpinan** |
| 4 | `jenis_simpanan` | Master | Kategori simpanan (Pokok, Wajib, Sukarela, SWP) |
| 5 | `simpanan` | Transaksi | Catatan setoran simpanan |
| 6 | `penarikan_simpanan` | Transaksi | Pencairan simpanan saat keluar koperasi |
| 7 | `periode_pinjaman` | Master | Periode pembukaan pinjaman + link guest |
| 8 | `pinjaman` | Transaksi | Data pinjaman + data bank + potongan 5% |
| 9 | `angsuran` | Transaksi | Jadwal cicilan bulanan |
| 10 | `potongan_bulanan` | Transaksi | Rekap potongan TPP per anggota per bulan |
| 11 | `log_aktivitas` | Audit | Catatan seluruh aktivitas |
| 12 | `pengaturan` | Konfigurasi | Pengaturan sistem + Maker-Checker |
| 13 | `perubahan_pengaturan` | Konfigurasi | Riwayat pengajuan perubahan |

---

## Detail Tabel

### 1. Tabel `bidang`

```sql
CREATE TABLE bidang (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_bidang VARCHAR(100)    NOT NULL COMMENT 'Nama bidang: Bina Marga, Cipta Karya, dll',
    keterangan  TEXT            NULL,
    created_at  TIMESTAMP       NULL,
    updated_at  TIMESTAMP       NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 2. Tabel `anggota`

**Fungsi:** Menyimpan data keanggotaan. **Bukan** untuk login — digunakan sebagai **data pencocokan** saat anggota mengajukan pinjaman via form guest (NIP dicocokkan dengan data ini).

```sql
CREATE TABLE anggota (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nip                 VARCHAR(20)     NOT NULL UNIQUE COMMENT 'NIP — identitas unik, digunakan untuk pencocokan',
    nama                VARCHAR(100)    NOT NULL,
    golongan            VARCHAR(10)     NOT NULL COMMENT 'Golongan PNS',
    jabatan             VARCHAR(100)    NULL,
    bidang_id           BIGINT UNSIGNED NOT NULL,
    alamat              TEXT            NULL,
    no_hp               VARCHAR(15)     NULL,
    tanggal_masuk       DATE            NOT NULL COMMENT 'Tanggal masuk koperasi',
    tanggal_keluar      DATE            NULL     COMMENT 'NULL = masih aktif',
    is_pendaftar_ulang  BOOLEAN         NOT NULL DEFAULT FALSE,
    status              ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at          TIMESTAMP       NULL,
    updated_at          TIMESTAMP       NULL,

    FOREIGN KEY (bidang_id) REFERENCES bidang(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> **Catatan:** Tidak ada kolom bank di sini — data bank dicatat **per pengajuan pinjaman**.

---

### 3. Tabel `users`

**Fungsi:** Akun login **hanya untuk Admin/Pengurus dan Pimpinan/Kepala**. Anggota biasa TIDAK memiliki akun.

```sql
CREATE TABLE users (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama                  VARCHAR(100)    NOT NULL        COMMENT 'Nama pengguna',
    username              VARCHAR(50)     NOT NULL UNIQUE COMMENT 'Username untuk login',
    password              VARCHAR(255)    NOT NULL        COMMENT 'Password di-hash bcrypt',

    -- Keamanan
    failed_login_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until          DATETIME        NULL,
    last_login_at         DATETIME        NULL,
    last_login_ip         VARCHAR(45)     NULL,

    role                  ENUM('admin','pimpinan') NOT NULL DEFAULT 'admin'
                          COMMENT 'Hanya admin dan pimpinan yang punya akun',
    remember_token        VARCHAR(100)    NULL,
    created_at            TIMESTAMP       NULL,
    updated_at            TIMESTAMP       NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> **Perubahan dari versi sebelumnya:** 
> - Dihapus: `anggota_id`, `must_change_pin`, `pin_changed_at`
> - Dihapus: role `anggota` — anggota TIDAK login ke sistem
> - `password` diganti dari PIN 6 digit menjadi password standar (lebih aman untuk admin)

---

### 4. Tabel `jenis_simpanan`

```sql
CREATE TABLE jenis_simpanan (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode            VARCHAR(10)     NOT NULL UNIQUE COMMENT 'POKOK, WAJIB, SUKARELA, SWP',
    nama            VARCHAR(50)     NOT NULL,
    nominal_default DECIMAL(15,2)   NOT NULL DEFAULT 0,
    is_wajib        BOOLEAN         NOT NULL DEFAULT FALSE,
    frekuensi       ENUM('sekali','bulanan','bebas','per_pinjaman') NOT NULL DEFAULT 'bebas',
    keterangan      TEXT            NULL,
    created_at      TIMESTAMP       NULL,
    updated_at      TIMESTAMP       NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Data awal (seeder):**

| kode | nama | nominal_default | is_wajib | frekuensi | keterangan |
|------|------|-----------------|----------|-----------|------------|
| POKOK | Simpanan Pokok | 50.000 | false | sekali | 1x saat daftar |
| WAJIB | Simpanan Wajib | 50.000 | true | bulanan | Wajib setiap bulan |
| SUKARELA | Simpanan Sukarela | 0 | false | bebas | Nominal bebas |
| SWP | Simpanan Wajib Pinjam | 0 | false | per_pinjaman | Otomatis 3% dari pinjaman |

> Semua simpanan **tidak bisa ditarik** selama anggota masih aktif. Dikembalikan **FULL** saat keluar.

---

### 5. Tabel `simpanan`

```sql
CREATE TABLE simpanan (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_referensi      VARCHAR(20)     NOT NULL UNIQUE COMMENT 'SIM-YYYY-NNNN',
    anggota_id        BIGINT UNSIGNED NOT NULL,
    jenis_simpanan_id BIGINT UNSIGNED NOT NULL,
    nominal           DECIMAL(15,2)   NOT NULL,
    tanggal           DATE            NOT NULL,
    bulan_untuk       TINYINT         NULL     COMMENT 'Bulan (khusus wajib)',
    tahun_untuk       SMALLINT        NULL     COMMENT 'Tahun (khusus wajib)',
    pinjaman_id       BIGINT UNSIGNED NULL     COMMENT 'Relasi ke pinjaman (khusus SWP)',
    keterangan        TEXT            NULL,
    dicatat_oleh      BIGINT UNSIGNED NULL,
    created_at        TIMESTAMP       NULL,
    updated_at        TIMESTAMP       NULL,

    FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON UPDATE CASCADE,
    FOREIGN KEY (jenis_simpanan_id) REFERENCES jenis_simpanan(id) ON UPDATE CASCADE,
    FOREIGN KEY (pinjaman_id) REFERENCES pinjaman(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (dicatat_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    UNIQUE KEY unique_wajib_bulanan (anggota_id, jenis_simpanan_id, bulan_untuk, tahun_untuk)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 6. Tabel `penarikan_simpanan`

**Fungsi:** Hanya untuk proses **keluar koperasi**. Tidak ada penarikan selama masih aktif.

```sql
CREATE TABLE penarikan_simpanan (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_referensi      VARCHAR(20)     NOT NULL UNIQUE COMMENT 'TRK-YYYY-NNNN',
    anggota_id        BIGINT UNSIGNED NOT NULL,
    jenis_simpanan_id BIGINT UNSIGNED NOT NULL,
    nominal           DECIMAL(15,2)   NOT NULL COMMENT 'FULL tanpa potongan',
    tanggal           DATE            NOT NULL,
    keterangan        TEXT            NULL,
    diproses_oleh     BIGINT UNSIGNED NOT NULL,
    created_at        TIMESTAMP       NULL,
    updated_at        TIMESTAMP       NULL,

    FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON UPDATE CASCADE,
    FOREIGN KEY (jenis_simpanan_id) REFERENCES jenis_simpanan(id) ON UPDATE CASCADE,
    FOREIGN KEY (diproses_oleh) REFERENCES users(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 7. Tabel `periode_pinjaman`

**Fungsi:** Periode pembukaan pinjaman. Setiap periode memiliki **token unik** untuk menghasilkan link guest yang bisa dibagikan ke anggota.

```sql
CREATE TABLE periode_pinjaman (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_periode          VARCHAR(100)    NOT NULL,
    tahun                 SMALLINT        NOT NULL,
    tanggal_buka          DATE            NOT NULL,
    tanggal_tutup         DATE            NULL     COMMENT 'NULL = belum ditentukan',
    batas_bulan_pelunasan TINYINT         NOT NULL DEFAULT 11,
    limit_per_anggota     DECIMAL(15,2)   NOT NULL COMMENT 'Maks pinjaman per anggota',
    token                 VARCHAR(64)     NOT NULL UNIQUE COMMENT 'Token unik untuk link pengajuan guest',
    catatan               TEXT            NULL,
    status                ENUM('buka','tutup','selesai') NOT NULL DEFAULT 'buka',
    dibuka_oleh           BIGINT UNSIGNED NOT NULL,
    created_at            TIMESTAMP       NULL,
    updated_at            TIMESTAMP       NULL,

    FOREIGN KEY (dibuka_oleh) REFERENCES users(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Mekanisme link guest:**
```
URL: https://domain.com/pinjaman/ajukan/{token}

Contoh: https://domain.com/pinjaman/ajukan/a1b2c3d4e5f6...

- Token di-generate otomatis saat periode dibuat (random 64 karakter)
- Link hanya bisa diakses jika periode berstatus BUKA
- Link tidak memerlukan login — siapapun dengan link bisa mengisi form
- Link dibagikan oleh pengurus ke anggota via WA/Email
```

---

### 8. Tabel `pinjaman`

**Fungsi:** Data pengajuan pinjaman. Berisi **data bank dari form guest** (per pengajuan, bukan permanen), potongan 5%, dan bunga 15% total.

```sql
CREATE TABLE pinjaman (
    id                     BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_referensi           VARCHAR(20)     NOT NULL UNIQUE COMMENT 'PJM-YYYY-NNNN',
    anggota_id             BIGINT UNSIGNED NOT NULL,
    periode_pinjaman_id    BIGINT UNSIGNED NOT NULL,
    bulan_pengajuan        TINYINT         NOT NULL,

    -- Data Bank (dari form guest, per pengajuan)
    nama_bank              VARCHAR(100)    NOT NULL COMMENT 'Nama bank: BRI, BNI, Mandiri, dll',
    nama_rekening          VARCHAR(100)    NOT NULL COMMENT 'Nama pemilik rekening',
    no_rekening            VARCHAR(30)     NOT NULL COMMENT 'Nomor rekening',

    -- Nominal & Bunga
    nominal_pinjaman       DECIMAL(15,2)   NOT NULL,
    bunga_persen           DECIMAL(5,2)    NOT NULL COMMENT 'Bunga TOTAL (default 15%)',
    total_bunga            DECIMAL(15,2)   NOT NULL COMMENT '= nominal × bunga%',
    tenor_bulan            INT             NOT NULL,

    -- Angsuran
    angsuran_pokok         DECIMAL(15,2)   NOT NULL COMMENT '= nominal ÷ tenor',
    angsuran_bunga         DECIMAL(15,2)   NOT NULL COMMENT '= total_bunga ÷ tenor',
    total_angsuran         DECIMAL(15,2)   NOT NULL COMMENT 'Per bulan = pokok + bunga',
    total_bayar            DECIMAL(15,2)   NOT NULL COMMENT '= nominal + total_bunga',

    -- Potongan 5% di Muka
    potongan_swp           DECIMAL(15,2)   NOT NULL COMMENT '3% → simpanan anggota',
    potongan_dana_resiko   DECIMAL(15,2)   NOT NULL COMMENT '1.5%',
    potongan_biaya_admin   DECIMAL(15,2)   NOT NULL COMMENT '0.5%',
    total_potongan         DECIMAL(15,2)   NOT NULL COMMENT '5%',
    dana_diterima          DECIMAL(15,2)   NOT NULL COMMENT '95%',

    -- Status & Approval
    tanggal_pengajuan      DATETIME        NOT NULL,
    tanggal_approval       DATETIME        NULL,
    status                 ENUM('menunggu','disetujui','ditolak','berjalan','lunas')
                           NOT NULL DEFAULT 'menunggu',
    approved_by            BIGINT UNSIGNED NULL,
    is_override            BOOLEAN         NOT NULL DEFAULT FALSE
                           COMMENT 'Override aturan 1 pinjaman/tahun',
    catatan                TEXT            NULL,
    created_at             TIMESTAMP       NULL,
    updated_at             TIMESTAMP       NULL,

    FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON UPDATE CASCADE,
    FOREIGN KEY (periode_pinjaman_id) REFERENCES periode_pinjaman(id) ON UPDATE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Contoh perhitungan (Rp 5.000.000, 8 bulan):**

| Komponen | Nilai |
|----------|-------|
| Nominal | Rp 5.000.000 |
| Bunga 15% | Rp 750.000 |
| Angsuran/bulan | Rp 718.750 (pokok 625rb + bunga 93,75rb) |
| Total bayar | Rp 5.750.000 |
| Potongan SWP (3%) | Rp 150.000 *(masuk simpanan)* |
| Potongan Resiko (1,5%) | Rp 75.000 |
| Potongan Admin (0,5%) | Rp 25.000 |
| **Dana diterima (95%)** | **Rp 4.750.000** |

---

### 9. Tabel `angsuran`

```sql
CREATE TABLE angsuran (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pinjaman_id         BIGINT UNSIGNED NOT NULL,
    angsuran_ke         INT             NOT NULL,
    nominal_pokok       DECIMAL(15,2)   NOT NULL,
    nominal_bunga       DECIMAL(15,2)   NOT NULL,
    nominal_total       DECIMAL(15,2)   NOT NULL,
    tanggal_jatuh_tempo DATE            NOT NULL,
    tanggal_bayar       DATE            NULL,
    status              ENUM('belum','lunas') NOT NULL DEFAULT 'belum',
    created_at          TIMESTAMP       NULL,
    updated_at          TIMESTAMP       NULL,

    FOREIGN KEY (pinjaman_id) REFERENCES pinjaman(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 10. Tabel `potongan_bulanan`

```sql
CREATE TABLE potongan_bulanan (
    id                       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    anggota_id               BIGINT UNSIGNED NOT NULL,
    bulan                    TINYINT         NOT NULL,
    tahun                    SMALLINT        NOT NULL,
    potongan_simpanan_wajib  DECIMAL(15,2)   NOT NULL DEFAULT 0,
    potongan_angsuran        DECIMAL(15,2)   NOT NULL DEFAULT 0,
    potongan_lainnya         DECIMAL(15,2)   NOT NULL DEFAULT 0,
    total_potongan           DECIMAL(15,2)   NOT NULL DEFAULT 0,
    catatan                  TEXT            NULL,
    status                   ENUM('draft','dikonfirmasi') NOT NULL DEFAULT 'draft',
    dicatat_oleh             BIGINT UNSIGNED NULL,
    created_at               TIMESTAMP       NULL,
    updated_at               TIMESTAMP       NULL,

    FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON UPDATE CASCADE,
    FOREIGN KEY (dicatat_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    UNIQUE KEY unique_anggota_bulan_tahun (anggota_id, bulan, tahun)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 11. Tabel `log_aktivitas`

```sql
CREATE TABLE log_aktivitas (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NULL     COMMENT 'NULL jika aksi oleh guest/sistem',
    aktivitas   VARCHAR(50)     NOT NULL,
    deskripsi   TEXT            NOT NULL,
    ip_address  VARCHAR(45)     NULL,
    data_lama   JSON            NULL,
    data_baru   JSON            NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_aktivitas (aktivitas),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 12. Tabel `pengaturan`

```sql
CREATE TABLE pengaturan (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`                   VARCHAR(50)     NOT NULL UNIQUE,
    value                   VARCHAR(255)    NOT NULL,
    deskripsi               VARCHAR(255)    NOT NULL,
    kategori                ENUM('keuangan','teknis') NOT NULL DEFAULT 'keuangan',
    memerlukan_persetujuan  BOOLEAN         NOT NULL DEFAULT TRUE,
    created_at              TIMESTAMP       NULL,
    updated_at              TIMESTAMP       NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Data awal:**

| key | value | deskripsi | kategori | persetujuan |
|-----|-------|-----------|----------|-------------|
| `bunga_pinjaman_persen` | 15 | Bunga TOTAL per pinjaman (%) | 🔴 keuangan | ✅ Ya |
| `potongan_swp_persen` | 3 | Potongan SWP (%) | 🔴 keuangan | ✅ Ya |
| `potongan_dana_resiko_persen` | 1.5 | Potongan dana resiko (%) | 🔴 keuangan | ✅ Ya |
| `potongan_biaya_admin_persen` | 0.5 | Potongan biaya admin (%) | 🔴 keuangan | ✅ Ya |
| `simpanan_pokok` | 50000 | Simpanan pokok saat daftar (Rp) | 🔴 keuangan | ✅ Ya |
| `simpanan_wajib` | 50000 | Simpanan wajib bulanan (Rp) | 🔴 keuangan | ✅ Ya |
| `batas_bulan_pelunasan_default` | 11 | Default bulan terakhir pelunasan | 🔴 keuangan | ✅ Ya |
| `tenor_minimal` | 1 | Tenor minimal (bulan) | 🔴 keuangan | ✅ Ya |
| `maks_percobaan_login` | 5 | Maks salah password | 🟢 teknis | ❌ Tidak |
| `durasi_kunci_akun_menit` | 30 | Durasi kunci akun | 🟢 teknis | ❌ Tidak |
| `session_timeout_menit` | 30 | Timeout sesi | 🟢 teknis | ❌ Tidak |

---

### 13. Tabel `perubahan_pengaturan`

```sql
CREATE TABLE perubahan_pengaturan (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pengaturan_id       BIGINT UNSIGNED NOT NULL,
    nilai_lama          VARCHAR(255)    NOT NULL,
    nilai_baru          VARCHAR(255)    NOT NULL,
    alasan              TEXT            NOT NULL,
    diajukan_oleh       BIGINT UNSIGNED NOT NULL,
    tanggal_pengajuan   DATETIME        NOT NULL,
    status              ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
    diputuskan_oleh     BIGINT UNSIGNED NULL,
    tanggal_keputusan   DATETIME        NULL,
    catatan_keputusan   TEXT            NULL,
    created_at          TIMESTAMP       NULL,
    updated_at          TIMESTAMP       NULL,

    FOREIGN KEY (pengaturan_id) REFERENCES pengaturan(id) ON UPDATE CASCADE,
    FOREIGN KEY (diajukan_oleh) REFERENCES users(id) ON UPDATE CASCADE,
    FOREIGN KEY (diputuskan_oleh) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Saldo Koperasi (Real-time)

```sql
-- Saldo = Dana Masuk − Dana Keluar
-- Dana Masuk: simpanan + angsuran lunas + dana resiko + biaya admin
-- Dana Keluar: pencairan pinjaman (95%) + penarikan saat keluar
```

---

## Format Nomor Referensi

| Transaksi | Format | Contoh |
|-----------|--------|--------|
| Simpanan | `SIM-{YYYY}-{NNNN}` | SIM-2026-0001 |
| Penarikan | `TRK-{YYYY}-{NNNN}` | TRK-2026-0001 |
| Pinjaman | `PJM-{YYYY}-{NNNN}` | PJM-2026-0001 |
