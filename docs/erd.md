# Entity Relationship Diagram (ERD)

## Deskripsi

ERD sistem koperasi simpan pinjam. **Anggota tidak memiliki akun login** — hanya admin/pengurus dan pimpinan/kepala yang login. Pengajuan pinjaman dilakukan oleh anggota via **link guest** yang dibagikan pengurus.

## Daftar Entitas

| No | Entitas | Kategori | Deskripsi |
|----|---------|----------|-----------|
| 1 | **Bidang** | Master | Bagian/bidang di Dinas PUPR |
| 2 | **Anggota** | Master | Data anggota (pencocokan NIP, bukan login) |
| 3 | **User** | Autentikasi | Akun login — hanya admin & pimpinan |
| 4 | **Jenis Simpanan** | Master | Pokok, Wajib, Sukarela, SWP |
| 5 | **Simpanan** | Transaksi | Catatan setoran |
| 6 | **Penarikan Simpanan** | Transaksi | Pencairan saat keluar koperasi |
| 7 | **Periode Pinjaman** | Master | Pembukaan pinjaman + token link guest |
| 8 | **Pinjaman** | Transaksi | Data pinjaman + bank + potongan 5% |
| 9 | **Angsuran** | Transaksi | Jadwal cicilan |
| 10 | **Potongan Bulanan** | Transaksi | Rekap potongan TPP |
| 11 | **Log Aktivitas** | Audit | Catatan aktivitas |
| 12 | **Pengaturan** | Konfigurasi | Konfigurasi + Maker-Checker |
| 13 | **Perubahan Pengaturan** | Konfigurasi | Riwayat perubahan |

---

## Diagram ERD

```mermaid
erDiagram
    BIDANG {
        bigint id PK
        varchar nama_bidang
        text keterangan
    }

    ANGGOTA {
        bigint id PK
        varchar nip UK "NIP — untuk pencocokan di form guest"
        varchar nama
        varchar golongan
        varchar jabatan
        bigint bidang_id FK
        text alamat
        varchar no_hp
        date tanggal_masuk
        date tanggal_keluar "NULL jika masih aktif"
        boolean is_pendaftar_ulang
        enum status "aktif / nonaktif"
    }

    USER {
        bigint id PK
        varchar nama
        varchar username UK "Username login"
        varchar password "Bcrypt hash"
        enum role "admin / pimpinan (TANPA anggota)"
        int failed_login_attempts
        datetime locked_until
        datetime last_login_at
    }

    JENIS_SIMPANAN {
        bigint id PK
        varchar kode UK "POKOK / WAJIB / SUKARELA / SWP"
        varchar nama
        decimal nominal_default
        boolean is_wajib
        enum frekuensi "sekali / bulanan / bebas / per_pinjaman"
    }

    SIMPANAN {
        bigint id PK
        varchar no_referensi UK
        bigint anggota_id FK
        bigint jenis_simpanan_id FK
        decimal nominal
        date tanggal
        tinyint bulan_untuk
        smallint tahun_untuk
        bigint pinjaman_id FK "Untuk SWP"
        bigint dicatat_oleh FK
    }

    PENARIKAN_SIMPANAN {
        bigint id PK
        varchar no_referensi UK
        bigint anggota_id FK
        bigint jenis_simpanan_id FK
        decimal nominal "FULL tanpa potongan"
        date tanggal
        bigint diproses_oleh FK
    }

    PERIODE_PINJAMAN {
        bigint id PK
        varchar nama_periode
        smallint tahun
        date tanggal_buka
        date tanggal_tutup
        tinyint batas_bulan_pelunasan "Default 11"
        decimal limit_per_anggota
        varchar token UK "Token unik untuk link guest"
        enum status "buka / tutup / selesai"
        bigint dibuka_oleh FK
    }

    PINJAMAN {
        bigint id PK
        varchar no_referensi UK
        bigint anggota_id FK
        bigint periode_pinjaman_id FK
        tinyint bulan_pengajuan
        varchar nama_bank "Dari form guest"
        varchar nama_rekening "Dari form guest"
        varchar no_rekening "Dari form guest"
        decimal nominal_pinjaman
        decimal bunga_persen "15% TOTAL"
        decimal total_bunga
        int tenor_bulan
        decimal angsuran_pokok
        decimal angsuran_bunga
        decimal total_angsuran "Per bulan"
        decimal total_bayar
        decimal potongan_swp "3%"
        decimal potongan_dana_resiko "1.5%"
        decimal potongan_biaya_admin "0.5%"
        decimal total_potongan "5%"
        decimal dana_diterima "95%"
        enum status "menunggu / disetujui / ditolak / berjalan / lunas"
        boolean is_override
        bigint approved_by FK
    }

    ANGSURAN {
        bigint id PK
        bigint pinjaman_id FK
        int angsuran_ke
        decimal nominal_pokok
        decimal nominal_bunga
        decimal nominal_total
        date tanggal_jatuh_tempo
        date tanggal_bayar
        enum status "belum / lunas"
    }

    POTONGAN_BULANAN {
        bigint id PK
        bigint anggota_id FK
        tinyint bulan
        smallint tahun
        decimal potongan_simpanan_wajib
        decimal potongan_angsuran
        decimal potongan_lainnya
        decimal total_potongan
        enum status "draft / dikonfirmasi"
        bigint dicatat_oleh FK
    }

    LOG_AKTIVITAS {
        bigint id PK
        bigint user_id FK "NULL jika guest/sistem"
        varchar aktivitas
        text deskripsi
        varchar ip_address
        json data_lama
        json data_baru
        timestamp created_at
    }

    PENGATURAN {
        bigint id PK
        varchar key UK
        varchar value
        varchar deskripsi
        enum kategori "keuangan / teknis"
        boolean memerlukan_persetujuan
    }

    PERUBAHAN_PENGATURAN {
        bigint id PK
        bigint pengaturan_id FK
        varchar nilai_lama
        varchar nilai_baru
        text alasan
        bigint diajukan_oleh FK "Admin — Maker"
        enum status "menunggu / disetujui / ditolak"
        bigint diputuskan_oleh FK "Pimpinan — Checker"
    }

    BIDANG ||--o{ ANGGOTA : "memiliki"
    ANGGOTA ||--o{ SIMPANAN : "menyetor"
    ANGGOTA ||--o{ PENARIKAN_SIMPANAN : "menerima saat keluar"
    JENIS_SIMPANAN ||--o{ SIMPANAN : "dikategorikan"
    JENIS_SIMPANAN ||--o{ PENARIKAN_SIMPANAN : "dikategorikan"
    ANGGOTA ||--o{ PINJAMAN : "mengajukan via guest"
    PERIODE_PINJAMAN ||--o{ PINJAMAN : "memiliki"
    PINJAMAN ||--o{ ANGSURAN : "dijadwalkan"
    PINJAMAN ||--o{ SIMPANAN : "menghasilkan SWP"
    ANGGOTA ||--o{ POTONGAN_BULANAN : "dipotong"
    USER ||--o{ PINJAMAN : "menyetujui"
    USER ||--o{ PENARIKAN_SIMPANAN : "memproses"
    USER ||--o{ LOG_AKTIVITAS : "melakukan"
    USER ||--o{ PERIODE_PINJAMAN : "membuka"
    PENGATURAN ||--o{ PERUBAHAN_PENGATURAN : "riwayat"
    USER ||--o{ PERUBAHAN_PENGATURAN : "mengajukan/memutuskan"
```

---

## Aliran Pengajuan Pinjaman (Guest)

```mermaid
flowchart LR
    A["Pengurus buka periode"] --> B["Sistem generate token/link"]
    B --> C["Pengurus share link<br/>ke anggota via WA/Email"]
    C --> D["Anggota buka link<br/>(GUEST tanpa login)"]
    D --> E["Isi form:<br/>NIP + Data Bank"]
    E --> F["Sistem cek NIP<br/>di tabel anggota"]
    F --> G["Pengajuan masuk<br/>antrian review"]
    G --> H["Pengurus review<br/>& approve/tolak"]
```

---

## Cek Status (Guest)

```mermaid
flowchart LR
    A["Anggota buka<br/>halaman cek status"] --> B["Input NIP"]
    B --> C["Sistem tampilkan<br/>pengajuan & status<br/>pinjaman anggota"]
```
