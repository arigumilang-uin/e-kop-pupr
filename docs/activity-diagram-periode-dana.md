# Activity Diagram — Pembukaan dan Pengelolaan Periode Pinjaman

## Deskripsi

Periode pembukaan pinjaman **dibuka dan ditutup secara manual** oleh pengurus/kepala koperasi. Bisa dibuka **berkali-kali dalam setahun** dengan rentang tanggal yang fleksibel. Anggota hanya bisa mengajukan pinjaman saat ada periode yang berstatus **BUKA**.

## Aturan Periode

| Aturan | Detail |
|--------|--------|
| **Pembukaan** | Manual oleh pengurus, bisa kapan saja |
| **Penutupan** | Manual, atau sesuai tanggal tutup yang ditentukan |
| **Frekuensi** | Bisa berkali-kali dalam setahun |
| **Limit** | Ditentukan per pembukaan (fleksibel) |
| **Tenor maks** | Default: dari bulan pengajuan sampai bulan pelunasan (default 11) |
| **Saldo koperasi** | Tidak ada minimal saldo untuk membuka periode |
| **Catatan** | Pengurus bisa menambahkan catatan/alasan pembukaan |

## Diagram: Membuka Periode Pinjaman Baru

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Pengurus membuka halaman<br/>'Kelola Periode Pinjaman'"]

    A --> B["Sistem menampilkan:<br/>• Daftar periode (aktif & riwayat)<br/>• Saldo koperasi saat ini<br/>• Tombol 'Buka Periode Baru'"]

    B --> C["Pengurus menekan<br/>'Buka Periode Baru'"]

    C --> D["Pengurus mengisi form:<br/>• Nama Periode: Pembukaan April 2026<br/>• Tanggal Buka: 15/04/2026<br/>• Tanggal Tutup: 20/04/2026<br/>  (opsional — bisa diisi nanti)<br/>• Limit per Anggota: Rp 5.000.000<br/>• Batas Bulan Pelunasan: 11 (Nov)<br/>• Catatan: (opsional)"]

    D --> E["Sistem menampilkan info:<br/>'📊 Saldo koperasi saat ini:<br/>Rp XX.XXX.XXX<br/><br/>Periode ini akan membuka<br/>pengajuan pinjaman maks<br/>Rp 5.000.000 per anggota.'"]

    E --> F["Pengurus menekan<br/>'Simpan & Buka Periode'"]

    F --> G["Periode tersimpan:<br/>• Status: BUKA<br/>• Anggota dapat mengajukan pinjaman"]

    G --> H["Log: 'Pengurus membuka periode<br/>pinjaman: Pembukaan April 2026<br/>Limit: Rp 5.000.000/anggota'"]

    H --> SELESAI([🔴 Selesai])
```

## Diagram: Menutup Periode Pinjaman

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Pengurus membuka halaman<br/>'Kelola Periode Pinjaman'"]

    A --> B["Pilih periode yang<br/>berstatus BUKA"]

    B --> C["Pengurus menekan<br/>'Tutup Periode'"]

    C --> D["Sistem mengonfirmasi:<br/>'Apakah yakin menutup periode ini?<br/>Anggota tidak bisa mengajukan<br/>pinjaman baru di periode ini.'"]

    D --> E{"Konfirmasi?"}
    E -- "Batal" --> SELESAI([🔴 Selesai])

    E -- "✅ Ya, tutup" --> F["Status diubah: TUTUP<br/>Tanggal tutup: hari ini"]

    F --> G["Log: 'Pengurus menutup<br/>periode pinjaman: ...'"]

    G --> SELESAI
```

## Contoh Timeline Periode dalam Setahun

```mermaid
gantt
    title Periode Pembukaan Pinjaman — Tahun 2026
    dateFormat  YYYY-MM-DD
    axisFormat  %b

    section Periode Pinjaman
    Pembukaan Maret     :done,    p1, 2026-03-27, 2026-03-29
    Pembukaan April     :done,    p2, 2026-04-15, 2026-04-20
    Pembukaan Juli      :active,  p3, 2026-07-01, 2026-07-10

    section Batas Pelunasan
    Pelunasan Terakhir  :milestone, m1, 2026-11-30, 0d
```

## Catatan Penting

- **Tidak ada validasi minimal saldo** saat membuka periode — validasi saldo terjadi saat **approval pinjaman**
- **Tanggal tutup bisa dikosongkan** — pengurus bisa menutup secara manual kapan saja
- Pengurus bisa **membuka periode baru** meskipun periode sebelumnya baru saja ditutup
- Setiap periode memiliki **limit per anggota sendiri** — bisa berbeda antar periode
- **Pinjaman yang sudah diajukan** di periode yang ditutup tetap bisa diproses (approval/tolak)
