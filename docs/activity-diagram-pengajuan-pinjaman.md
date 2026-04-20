# Activity Diagram — Pengajuan Pinjaman (Guest Form via Link)

## Deskripsi

Anggota mengajukan pinjaman **tanpa login** — melalui **link guest** yang dibagikan oleh pengurus saat periode pinjaman dibuka. Sistem mencocokkan NIP yang diinput dengan data anggota yang sudah ada di sistem.

## Alur Lengkap

```
Pengurus buka periode → Sistem generate link unik
→ Pengurus share link ke anggota (WA/Email)
→ Anggota buka link → Isi form (NIP + bank + nominal + tenor)
→ Sistem cocokkan NIP → Cek kelayakan otomatis
→ Pengajuan masuk antrian → Pengurus review & approve/tolak
```

## Diagram: Anggota Mengajukan Pinjaman via Link Guest

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Anggota menerima link dari pengurus<br/>(via WA/Email)"]

    A --> B["Anggota membuka link:<br/>domain.com/pinjaman/ajukan/{token}"]

    B --> B1{"Apakah link valid?<br/>(token ada dan<br/>periode berstatus BUKA)"}
    B1 -- "❌ Tidak valid / sudah tutup" --> B1a["Pesan: 'Periode pengajuan<br/>sudah ditutup atau link tidak valid.'"]
    B1a --> SELESAI([🔴 Selesai])

    B1 -- "✅ Valid & buka" --> C["Sistem menampilkan halaman form<br/>dengan info periode:<br/>• Nama Periode<br/>• Limit per anggota: Rp X<br/>• Bunga: 15%<br/>• Potongan: 5% (SWP, Resiko, Admin)<br/>• Tenor maks: dari bulan ini s/d bulan 11"]

    C --> D["Anggota mengisi form pengajuan:<br/>━━━━ Data Identitas ━━━━<br/>• NIP<br/>• Nama lengkap<br/>━━━━ Data Bank ━━━━<br/>• Nama Bank (dropdown)<br/>• Nama Rekening<br/>• Nomor Rekening<br/>━━━━ Data Pinjaman ━━━━<br/>• Nominal yang diajukan<br/>• Tenor pembayaran (bulan)"]

    D --> E["Anggota menekan 'Ajukan'"]

    E --> F{"CEK 1:<br/>Apakah NIP terdaftar<br/>di data anggota?"}
    F -- "❌ Tidak ditemukan" --> F1["Pesan: 'NIP tidak terdaftar<br/>sebagai anggota koperasi.<br/>Hubungi pengurus.'"]
    F1 --> SELESAI

    F -- "✅ Ditemukan" --> F2{"Apakah nama yang diisi<br/>cocok dengan data anggota?"}
    F2 -- "❌ Tidak cocok" --> F2a["Pesan: 'Nama tidak cocok<br/>dengan data NIP yang terdaftar.'"]
    F2a --> SELESAI

    F2 -- "✅ Cocok" --> G{"CEK 2:<br/>Apakah status anggota<br/>masih AKTIF?"}
    G -- "❌ Nonaktif" --> G1["Pesan: 'Akun keanggotaan Anda<br/>tidak aktif. Hubungi pengurus.'"]
    G1 --> SELESAI

    G -- "✅ Aktif" --> H{"CEK 3:<br/>Apakah anggota sudah<br/>punya pinjaman aktif<br/>di tahun ini?"}
    H -- "⚠️ Sudah punya" --> H1["Pesan: 'Anda masih memiliki<br/>pinjaman aktif di tahun ini.<br/>Maks 1 pinjaman per tahun.'<br/><br/>Pengajuan tetap bisa disubmit<br/>tapi ditandai perlu OVERRIDE<br/>dari pengurus."]
    H1 --> I

    H -- "✅ Belum punya" --> I{"CEK 4:<br/>Apakah nominal ≤<br/>limit per anggota?"}
    I -- "❌ Melebihi" --> I1["Pesan: 'Nominal melebihi batas<br/>Rp X per anggota.'"]
    I1 --> D

    I -- "✅ Dalam limit" --> J["Sistem menampilkan PREVIEW:<br/>━━━━━━━━━━━━━━━━━━━━━━━━<br/>📋 Ringkasan Pengajuan<br/>• Nominal: Rp 5.000.000<br/>• Bunga 15%: Rp 750.000<br/>• Angsuran/bulan: Rp 718.750<br/>• Total bayar: Rp 5.750.000<br/>━━━━━━━━━━━━━━━━━━━━━━━━<br/>✂️ Potongan di Muka (5%)<br/>• SWP (3%): Rp 150.000<br/>• Dana Resiko (1,5%): Rp 75.000<br/>• Biaya Admin (0,5%): Rp 25.000<br/>━━━━━━━━━━━━━━━━━━━━━━━━<br/>💵 Dana Diterima: Rp 4.750.000<br/>🏦 Ke rekening: BRI - 1234567890"]

    J --> K["Anggota menekan<br/>'Konfirmasi Pengajuan'"]

    K --> L["Sistem menyimpan pengajuan:<br/>• No. Ref: PJM-2026-XXXX<br/>• Status: MENUNGGU<br/>• Data bank tercatat"]

    L --> M["Halaman konfirmasi:<br/>'Pengajuan berhasil disubmit!<br/>No. Referensi: PJM-2026-XXXX<br/>Status: Menunggu review pengurus.<br/><br/>Anda bisa cek status di:<br/>domain.com/pinjaman/status'"]

    M --> SELESAI
```

## Diagram: Pengurus Review & Approve

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Pengurus login<br/>→ menu Pengajuan Pinjaman"]

    A --> B["Sistem menampilkan daftar<br/>pengajuan status MENUNGGU"]

    B --> C["Pengurus memilih pengajuan"]

    C --> D["Detail pengajuan:<br/>• Data anggota (NIP, nama, bidang)<br/>• Data bank (bank, rekening)<br/>• Nominal, tenor, angsuran<br/>• Potongan 5% & dana diterima<br/>• Riwayat pinjaman sebelumnya"]

    D --> D1["📊 INFO SALDO:<br/>'Saldo koperasi: Rp XX.XXX.XXX<br/>Pinjaman diajukan: Rp X.XXX.XXX<br/>Sisa setelah approve: Rp XX.XXX.XXX'"]

    D1 --> D2{"Saldo mencukupi?"}
    D2 -- "⚠️ TIDAK CUKUP" --> D2a["⚠️ WARNING:<br/>'Saldo tidak mencukupi.<br/>Tetap bisa di-approve<br/>jika pengurus memutuskan.'"]
    D2a --> E
    D2 -- "✅ Cukup" --> E

    E{"Keputusan?"}
    E -- "❌ Tolak" --> F["Isi alasan → Status: DITOLAK"]
    F --> SELESAI([🔴 Selesai])

    E -- "✅ Setujui" --> G["Status: BERJALAN"]
    G --> H["Sistem otomatis:<br/>1. Buat jadwal angsuran<br/>2. Catat SWP ke simpanan<br/>3. Catat potongan resiko & admin"]
    H --> SELESAI
```

## Diagram: Anggota Cek Status (Guest)

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Anggota buka halaman<br/>domain.com/pinjaman/status"]

    A --> B["Input NIP"]

    B --> C{"NIP ditemukan?"}
    C -- "❌ Tidak" --> C1["Pesan: 'NIP tidak ditemukan.'"]
    C1 --> SELESAI([🔴 Selesai])

    C -- "✅ Ditemukan" --> D["Sistem menampilkan:<br/>━━━ Pengajuan Aktif ━━━<br/>• PJM-2026-0012<br/>  Status: MENUNGGU<br/>  Nominal: Rp 5.000.000<br/>━━━ Pinjaman Berjalan ━━━<br/>• PJM-2025-0008<br/>  Sisa: 3 angsuran<br/>━━━ Riwayat ━━━<br/>• PJM-2024-0003: LUNAS ✅"]

    D --> SELESAI
```

## Pengecekan Otomatis (Ringkasan)

| Tahap | Dicek | Jika Gagal |
|-------|-------|------------|
| 1 | NIP terdaftar & nama cocok | Tolak: "NIP tidak terdaftar" |
| 2 | Anggota masih aktif | Tolak: "Akun tidak aktif" |
| 3 | Belum punya pinjaman aktif tahun ini | Tandai: perlu override pengurus |
| 4 | Nominal ≤ limit per anggota | Tolak: "Melebihi batas" |
| Saat approval | Saldo koperasi mencukupi | Warning (bisa override) |
