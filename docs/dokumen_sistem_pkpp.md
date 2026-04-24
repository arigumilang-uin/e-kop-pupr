# Dokumentasi Sistem E-Kop PKPP Riau (Koperasi Simpan Pinjam)

Dokumen ini berisi informasi teknis dan arsitektur Sistem Informasi Koperasi Simpan Pinjam PKPP Riau untuk acuan kolaborasi tim pengembang.

## 1. Teknologi (Tech Stack)
*   **Framework Backend:** Laravel 13 (PHP 8.3+)
*   **Database:** MySQL (Melalui Laravel Sail / Docker)
*   **Frontend:** Blade Templating + Tailwind CSS v4 (Via Vite)
*   **Development Environment:** Laravel Sail (Docker based)

## 2. Arsitektur Kode
Proyek ini mengadopsi pola MVC (Model-View-Controller) dengan penambahan Pattern Service dan Enum untuk merapikan *Business Logic* dan mempermudah pembacaan status.

*   **Controllers (`app/Http/Controllers`):** Dikelompokkan berdasarkan modul (Auth, Dashboard, Keuangan, Master, Periode, Pinjaman, Simulasi, Sistem).
*   **Models (`app/Models`):** Menggunakan Eloquent, setiap relasi didefinisikan dengan *return type* yang jelas (BelongsTo, HasMany, dll).
*   **Services (`app/Services`):** Tempat berkumpulnya *Business Logic* agar controller tidak gemuk:
    *   `PinjamanService`: Mengelola logika persetujuan pinjaman, pembuatan angsuran otomatis (pokok & bunga flat), pemotongan biaya.
    *   `SaldoService`: Menghitung total kas koperasi yang *real-time* (Simpanan masuk + Angsuran Lunas + Fee - Pencairan - Penarikan).
    *   `PengaturanService`: Membaca pengaturan dinamis (Bunga, SWP, Limit) dari tabel `pengaturan` (implementasi dengan mekanisme Cache Server).
    *   `ActivityLogService`: Logging pencatatan siapa melakukan apa secara *global*.
*   **Enums (`app/Enums`):** Digunakan untuk standarisasi Status pada database (seperti `StatusPinjaman`, `StatusAnggota`, `StatusAngsuran`, `RoleUser`) yang didukung langsung oleh casting eloquent Laravel modern.
*   **Traits (`app/Traits`):** Helper *reusable* untuk model seperti pembuatan Nomor Referensi Otomatis (`HasNoReferensi`) dan Formatting Rupiah (`HasRupiahFormat`).

## 3. Skema Database (Entitas Utama)
Sistem memiliki 13 entitas inti di dalam *Database*:

1.  **Pengguna & Master Data:**
    *   `users`: Menyimpan kredensial admin dan pimpinan (Role-based).
    *   `bidang`: Master data unit kerja dinas.
    *   `anggota`: Menyimpan NIP, Nama, dan status keanggotaan (Tidak punya password, bukan user login sistem. Autentikasi berbasis NIP saat pengajuan external).
2.  **Keuangan & Simpanan:**
    *   `jenis_simpanan`: Pokok, Wajib, Sukarela, dan SWP.
    *   `simpanan`: Catatan detail arus uang masuk dari simpanan anggota.
    *   `penarikan_simpanan`: Catatan arus uang keluar saat anggota pensiun/berhenti.
3.  **Pinjaman (Kredit):**
    *   `periode_pinjaman`: Koperasi membuka periode tertentu. Terdapat *Token unik* (`token`) yang digunakan untuk form pendaftaran guest/anggota dari luar sistem.
    *   `pinjaman`: Menyimpan data pengajuan, bank tujuan cair, nominal, pemotongan awal (Dana Risiko, Biaya Admin, Simpanan Wajib Pinjaman/SWP), dan status.
    *   `angsuran`: Di-_generate_ otomatis saat pinjaman disetujui. Berjalan bulan demi bulan.
    *   `potongan_bulanan`: Menyimpan rekap TPP, mengintegrasikan tagihan simpanan wajib dan angsuran berjalannya anggota per bulan sebelum dieksekusi potong.
4.  **Sistem & Audit:**
    *   `pengaturan` & `perubahan_pengaturan`: Konfigurasi *dynamic* dengan konsep Maker-Checker (Admin mengajukan ubah persentase, Pimpinan approve).
    *   `log_aktivitas`: Penyimpanan riwayat aktivitas seluruh transaksional CRUD.

## 4. Alur Bisnis (Business Process Main Flows)

### A. Pengajuan Pinjaman (Guest Flow)
1.  Admin membuka `periode_pinjaman` dan membagikan Link Form beralamat `.../pinjaman/ajukan/{token}`.
2.  Anggota mengakses link (tanpa perlu login), memasukkan NIP. Sistem memvalidasi apakah NIP terdaftar di tabel `anggota` dan tidak sedang memiliki pinjaman berjalan.
3.  Anggota melengkapi formulir nominal, tenor, dan data bank pencairan. Status awal = `menunggu`. Anggota mendapat Nomor Referensi.
4.  Anggota bisa melacak status pinjaman melalui fitur *"Cek Status Pinjaman"* atau membatalkan sendiri jika status masih menunggu.

### B. Persetujuan & Pencairan Pinjaman (Admin Flow)
1.  Admin mereview data pinjaman. Jika disetujui, `PinjamanService` akan dipanggil.
2.  Di tahap ini, sistem langsung memotong nominal pinjaman sebesar **5% (Default)** untuk:
    *   Simpanan Wajib Pinjaman (SWP) = 3% (uang masuk ke saldo koperasi sebagai simpanan anggota).
    *   Dana Resiko = 1.5% (masuk kas sebagai pendapatan).
    *   Biaya Admin = 0.5% (masuk kas sebagai pendapatan).
    *   Sehingga anggota hanya menerima cair dana 95%.
3.  Sistem langsung menjabarkan cicilan (Generate `angsuran`) sesuai dengan tenor dengan pembagian pokok & bunga flat (Default 15% dari Pokok Fix dibagi tenor).

### C. Potongan TPP Bulanan (Kolektif)
1.  Admin fitur `potongan_bulanan` akan memuat data bulanan ke depan.
2.  Sistem membaca seluruh `anggota` aktif, menarik tagihan Simpanan Wajib dan tagihan `angsuran` berjalan bulan tersebut.
3.  Admin mengonfirmasi (mem-proses) eksekusi. Sistem akan mengubah status angsuran menjadi "Lunas" dan menambahkan mutasi "Simpanan" secara kolektif bulk.

## 5. Fitur Analitik yang Dibangun
Sistem memuat pengolahan data analitis tingkat lanjut berbasis Laravel Collection & Builder:
1.  **Laporan Keuangan Detail (`/keuangan/laporan`):** Terdapat 4 segmentasi tab real-time:
    *   *Ringkasan Aset:* Perpaduan Kas Liquid Tersedia vs Piutang Beredar.
    *   *Piutang Koperasi:* Progress bar tagihan utang per orang, kapan lunas, riwayat bayar.
    *   *Simpanan Anggota:* Tabel Pivot seluruh kepemilikan simpanan anggota vs Jenisnya.
    *   *Arus Kas:* Menunjukkan debit vs kredit rill (Pemasukan vs Pencairan).
2.  **Simulasi Proyeksi (`/keuangan/simulasi`):** Alat bagi pengurus untuk meramal kapasitas kas finansial koperasi di N-Bulan ke depan. Logika akan menambahkan pemasukan (Simpanan Wajib * jumlah anggota aktif + Estimasi angsuran masuk) secara bulanan pada proyeksi berjalan untuk memutuskan apakah kas cukup untuk membuka termin pinjaman baru.
3.  **Arsip Transaksi (`/keuangan/arsip`):** Histori real-time mutasi Simpanan, Angsuran Lunas, dan Pinjaman Dicairkan dalam range waktu yang dapat di-pilih (Custom Date filter, This Week, This Month).

## 6. Development & Deployment

### Menjalankan Project (Local)
Sistem menggunakan `laravel/sail` untuk setup development tanpa harus menginstall PHP dan MySQL di local host PC.

```bash
# Clone repository
git clone [repo_url]
cd e-kop-pkpp-riau

# Instalasi awal dependency PHP
composer install

# Copy enviroment variables
cp .env.example .env

# Jalankan Sail Container
./vendor/bin/sail up -d

# Generate key & Migrate DB beserta Seeder master
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed

# Compilasi assets frontend
npm install
npm run dev
```

### Kredensial Default (DatabaseSeeder)
*   **Admin:** `username: admin` | `password: admin123`
*   **Pimpinan:** `username: pimpinan` | `password: pimpinan123`

---
*Dokumen ini diperbarui terakhir kali sejalan dengan penambahan fitur Arsip Transaksi dan Simulasi Proyeksi Keuangan.*
