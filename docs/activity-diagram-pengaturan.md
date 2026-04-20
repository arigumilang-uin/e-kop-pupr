# Activity Diagram — Pengaturan Sistem (Maker-Checker)

## Deskripsi

Pengaturan sistem dibagi 2 kategori: **keuangan** (butuh persetujuan Pimpinan) dan **teknis** (admin bisa ubah langsung). Pola **Maker-Checker** menjamin bahwa perubahan konfigurasi keuangan yang berdampak pada uang anggota tidak bisa dilakukan sepihak.

## Daftar Pengaturan

### 🔴 Pengaturan Keuangan (Perlu Persetujuan Pimpinan)

| Key | Default | Deskripsi |
|-----|---------|-----------|
| bunga_pinjaman_persen | 15 | Bunga TOTAL per pinjaman (%) |
| potongan_swp_persen | 3 | Potongan SWP dari pinjaman (%) |
| potongan_dana_resiko_persen | 1.5 | Potongan dana resiko (%) |
| potongan_biaya_admin_persen | 0.5 | Potongan biaya admin (%) |
| simpanan_pokok | 50000 | Simpanan pokok saat daftar (Rp) |
| simpanan_wajib | 50000 | Simpanan wajib bulanan (Rp) |
| batas_bulan_pelunasan_default | 11 | Bulan terakhir pelunasan |
| tenor_minimal | 1 | Tenor minimal (bulan) |

### 🟢 Pengaturan Teknis (Admin Bisa Ubah Langsung)

| Key | Default | Deskripsi |
|-----|---------|-----------|
| maks_percobaan_login | 5 | Maks salah PIN sebelum kunci |
| durasi_kunci_akun_menit | 30 | Durasi kunci akun (menit) |
| session_timeout_menit | 30 | Timeout sesi (menit) |

## Diagram: Admin Mengajukan Perubahan Pengaturan Keuangan

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Admin membuka<br/>'Pengaturan Sistem'"]

    A --> B["Sistem menampilkan daftar<br/>pengaturan beserta kategorinya"]

    B --> C["Admin memilih pengaturan<br/>yang ingin diubah"]

    C --> D{"Apakah pengaturan<br/>ini kategori KEUANGAN?"}

    D -- "🟢 Teknis" --> E1["Admin mengubah nilai langsung"]
    E1 --> E2["Sistem menyimpan perubahan"]
    E2 --> E3["Log: 'Admin mengubah<br/>session_timeout dari 30 ke 60 menit'"]
    E3 --> SELESAI([🔴 Selesai])

    D -- "🔴 Keuangan" --> F{"Apakah sudah ada<br/>pengajuan MENUNGGU<br/>untuk pengaturan ini?"}
    F -- "⚠️ Sudah ada" --> F1["'Sudah ada pengajuan perubahan<br/>yang belum diputuskan. Tunggu<br/>sampai Pimpinan memutuskan.'"]
    F1 --> SELESAI

    F -- "✅ Belum ada" --> G["Admin mengisi form:<br/>• Nilai baru<br/>• Alasan perubahan (WAJIB)"]

    G --> H["Sistem menyimpan pengajuan:<br/>Status: MENUNGGU<br/>Maker: Admin"]

    H --> I["Notifikasi ke Pimpinan:<br/>'Ada pengajuan perubahan<br/>pengaturan yang perlu<br/>Anda tinjau.'"]

    I --> J["Log: 'Admin mengajukan perubahan<br/>bunga_pinjaman_persen<br/>dari 15% menjadi 12%'"]

    J --> SELESAI
```

## Diagram: Pimpinan Menyetujui/Menolak Perubahan

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Pimpinan membuka<br/>'Persetujuan Pengaturan'"]

    A --> B["Sistem menampilkan daftar<br/>pengajuan yang MENUNGGU"]

    B --> C["Pimpinan memilih pengajuan"]

    C --> D["Sistem menampilkan detail:<br/>• Pengaturan: Bunga Pinjaman<br/>• Nilai Lama: 15%<br/>• Nilai Baru: 12%<br/>• Alasan: Hasil rapat pengurus<br/>• Diajukan oleh: Admin A<br/>• Tanggal: 10/04/2026<br/><br/>⚠️ Perubahan hanya berlaku<br/>untuk transaksi BARU"]

    D --> E{"Keputusan Pimpinan?"}

    E -- "❌ Tolak" --> F["Pimpinan mengisi alasan<br/>penolakan (WAJIB)"]
    F --> G["Status: DITOLAK<br/>Nilai TIDAK berubah"]
    G --> H1["Log: 'Pimpinan menolak perubahan<br/>bunga — Alasan: Belum tepat'"]
    H1 --> SELESAI([🔴 Selesai])

    E -- "✅ Setujui" --> I["Pimpinan menekan 'Setujui'"]
    I --> J["Status: DISETUJUI<br/>Sistem otomatis mengubah<br/>nilai pengaturan"]
    J --> K["Log: 'Pimpinan menyetujui<br/>perubahan bunga_pinjaman_persen<br/>dari 15% menjadi 12%'"]
    K --> SELESAI
```

## Aturan Penting

- Perubahan **tidak retroaktif** — hanya berlaku untuk transaksi baru
- Hanya boleh ada **1 pengajuan aktif** per pengaturan
- **Alasan wajib diisi** baik saat mengajukan maupun menolak
- Semua perubahan **tercatat di log aktivitas** dengan nilai lama dan baru
