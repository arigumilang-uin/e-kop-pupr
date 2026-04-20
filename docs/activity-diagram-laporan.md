# Activity Diagram — Pembuatan Laporan Keuangan

## Deskripsi Proses

Proses pembuatan laporan keuangan koperasi yang dapat di-generate oleh **Admin Koperasi** kapan saja. Laporan dihasilkan dalam **dua format**: PDF (untuk dibaca dan dicetak) dan Excel (untuk diolah lebih lanjut). Sistem menyediakan beberapa jenis laporan yang mencakup seluruh aspek operasional koperasi.

## Aktor yang Terlibat

- **Admin Koperasi** — Memilih jenis laporan, filter periode, dan meng-generate laporan
- **Pimpinan Koperasi** — Melihat dan mengunduh laporan yang sudah dibuat
- **Sistem** — Mengambil data, memproses perhitungan, dan menghasilkan dokumen laporan

## Diagram

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Admin membuka halaman<br/>'Laporan Keuangan'"]

    A --> B["Admin memilih jenis laporan<br/>yang ingin dibuat"]

    B --> C{"Jenis laporan<br/>yang dipilih?"}

    C --> C1["📋 Laporan Simpanan<br/>Rekap seluruh simpanan anggota<br/>(pokok, wajib, sukarela)"]
    C --> C2["💰 Laporan Pinjaman<br/>Rekap seluruh pinjaman<br/>(aktif, lunas, ditolak)"]
    C --> C3["📅 Laporan Angsuran & Potongan TPP<br/>Rekap pembayaran angsuran<br/>dan potongan TPP per bulan"]
    C --> C4["📊 Laporan Neraca Keuangan<br/>Posisi keuangan koperasi:<br/>aset, kewajiban, dan modal"]
    C --> C5["📈 Laporan Dana Periode<br/>Rekap penggunaan dana<br/>per periode"]

    C1 --> D["Admin menentukan filter:<br/>• Periode waktu (bulan/tahun)<br/>• Bidang/bagian (semua atau spesifik)<br/>• Status anggota (aktif/nonaktif/semua)"]
    C2 --> D
    C3 --> D
    C4 --> D
    C5 --> D

    D --> E["Admin menekan tombol<br/>'Generate Laporan'"]

    E --> F["Sistem mengambil data dari database<br/>sesuai jenis dan filter yang dipilih"]

    F --> G["Sistem memproses dan menghitung<br/>total, subtotal, dan ringkasan data"]

    G --> H["Sistem menghasilkan laporan<br/>dalam dua format:"]

    H --> H1["📄 Format PDF<br/>• Tampilan formal dan rapi<br/>• Siap cetak<br/>• Dilengkapi kop surat dinas<br/>• Tanda tangan pengesahan"]
    H --> H2["📊 Format Excel<br/>• Data mentah dalam tabel<br/>• Mudah diolah dan difilter<br/>• Bisa dimodifikasi jika perlu<br/>• Formula otomatis untuk total"]

    H1 --> I["Sistem menampilkan preview<br/>laporan kepada Admin"]
    H2 --> I

    I --> J["Admin dapat:<br/>• 📥 Mengunduh file laporan<br/>• 🖨️ Mencetak langsung<br/>• 📤 Membagikan ke Pimpinan"]

    J --> SELESAI([🔴 Selesai])
```

## Jenis-jenis Laporan

### 1. 📋 Laporan Simpanan
| Informasi | Keterangan |
|-----------|------------|
| Konten | Daftar seluruh anggota beserta total simpanan (pokok + wajib + sukarela) |
| Filter | Per bulan, per tahun, per bidang |
| Kegunaan | Mengetahui total dana simpanan yang terkumpul |

### 2. 💰 Laporan Pinjaman
| Informasi | Keterangan |
|-----------|------------|
| Konten | Daftar semua pinjaman, nominal, tenor, status, sisa angsuran |
| Filter | Per periode, per status, per bidang |
| Kegunaan | Memantau total pinjaman yang beredar dan yang sudah lunas |

### 3. 📅 Laporan Angsuran & Potongan TPP
| Informasi | Keterangan |
|-----------|------------|
| Konten | Rekap potongan TPP setiap peminjam per bulan |
| Filter | Per bulan, per anggota |
| Kegunaan | Dokumen resmi potongan TPP untuk arsip dinas dan koperasi |

### 4. 📊 Laporan Neraca Keuangan
| Informasi | Keterangan |
|-----------|------------|
| Konten | Posisi keuangan: total aset (piutang + kas), kewajiban (simpanan anggota), modal (SHU) |
| Filter | Per bulan, per tahun |
| Kegunaan | Mengetahui kesehatan keuangan koperasi secara keseluruhan |

### 5. 📈 Laporan Dana Periode
| Informasi | Keterangan |
|-----------|------------|
| Konten | Total dana awal, total yang sudah dipinjamkan, sisa dana, jumlah peminjam |
| Filter | Per periode |
| Kegunaan | Memantau penggunaan alokasi dana koperasi per periode |

## Catatan Penting

- Laporan dapat di-generate **kapan saja** — tidak harus menunggu akhir bulan atau akhir periode
- Format **PDF** dirancang agar formal dan bisa langsung dicetak sebagai dokumen resmi
- Format **Excel** dirancang agar data bisa diolah lebih lanjut jika terdapat kendala teknis di sistem
- Semua laporan yang pernah di-generate tersimpan di sistem dan bisa diunduh ulang
