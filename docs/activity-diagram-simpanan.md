# Activity Diagram — Proses Simpanan

## Deskripsi

Dokumen ini mencakup seluruh proses yang berkaitan dengan simpanan anggota koperasi. Terdapat **4 jenis simpanan** dan **3 proses utama**.

## Jenis Simpanan

| Jenis | Kode | Nominal | Kapan | Bisa Ditarik? |
|-------|------|---------|-------|---------------|
| **Simpanan Pokok** | POKOK | Rp 50.000 | 1x saat daftar | ❌ Tidak (kecuali keluar) |
| **Simpanan Wajib** | WAJIB | Rp 50.000/bulan | Setiap bulan | ❌ Tidak (kecuali keluar) |
| **Simpanan Sukarela** | SUKARELA | Bebas | Kapan saja | ❌ Tidak (kecuali keluar) |
| **SWP** | SWP | 3% dari pinjaman | Otomatis saat pinjam | ❌ Tidak (kecuali keluar) |

> ⚠️ **Aturan utama:** SEMUA jenis simpanan **tidak bisa ditarik** selama anggota masih aktif. Simpanan hanya dikembalikan **FULL tanpa potongan** saat anggota **keluar dari koperasi**.

---

## Proses 1: Pencatatan Simpanan Pokok (Saat Daftar)

```mermaid
flowchart TD
    A["Admin mendaftarkan<br/>anggota baru"] --> B["Sistem otomatis mencatat<br/>simpanan pokok:<br/>• Nominal: Rp 50.000<br/>• No. Ref: SIM-2026-XXXX<br/>• Jenis: POKOK"]
    B --> C["Simpanan pokok tercatat<br/>di rekening anggota"]
```

---

## Proses 2: Pencatatan Simpanan Wajib Bulanan

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Admin membuka halaman<br/>'Catat Simpanan Wajib'"]

    A --> B{"Catat per orang<br/>atau massal?"}

    B -- "👤 Per orang" --> C["Admin memilih anggota<br/>dan bulan yang dicatat"]
    C --> C1{"Sudah dicatat<br/>untuk bulan ini?"}
    C1 -- "⚠️ Sudah" --> C1a["Pesan: 'Simpanan wajib<br/>bulan ini sudah tercatat.'"]
    C1a --> SELESAI([🔴 Selesai])
    C1 -- "✅ Belum" --> D

    B -- "👥 Massal" --> E["Admin memilih bulan & tahun"]
    E --> E1["Sistem menampilkan daftar<br/>anggota aktif yang BELUM<br/>tercatat simpanan wajib<br/>untuk bulan tersebut"]
    E1 --> E2["Admin memilih anggota<br/>(semua atau sebagian)"]
    E2 --> D

    D["Sistem mencatat simpanan wajib:<br/>• Nominal: Rp 50.000<br/>• No. Ref: SIM-2026-XXXX<br/>• Jenis: WAJIB<br/>• Bulan/Tahun: sesuai pilihan"]
    D --> F["Tercatat juga di<br/>potongan bulanan anggota"]
    F --> SELESAI
```

---

## Proses 3: Pencatatan Simpanan Sukarela

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Admin membuka halaman<br/>'Catat Simpanan Sukarela'"]
    A --> B["Admin memilih anggota<br/>dan memasukkan nominal<br/>(bebas, tidak ada minimal)"]
    B --> C["Sistem mencatat:<br/>• Nominal: sesuai input<br/>• No. Ref: SIM-2026-XXXX<br/>• Jenis: SUKARELA"]
    C --> SELESAI([🔴 Selesai])
```

---

## Proses SWP (Simpanan Wajib Pinjam — Otomatis)

SWP **tidak dicatat manual** oleh admin. SWP otomatis tercatat saat **pinjaman disetujui**:

```mermaid
flowchart TD
    A["Pinjaman PJM-2026-XXXX<br/>DISETUJUI"] --> B["Sistem otomatis mencatat SWP:<br/>• Nominal: 3% dari pinjaman<br/>• No. Ref: SIM-2026-XXXX<br/>• Jenis: SWP<br/>• Relasi: pinjaman_id"]
    B --> C["SWP masuk ke saldo<br/>simpanan anggota<br/>(dikembalikan saat keluar)"]
```

---

## Proses Keluar Koperasi (Pengembalian Semua Simpanan)

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Anggota mengajukan<br/>permohonan keluar koperasi"]

    A --> B["Admin membuka halaman<br/>'Proses Keluarnya Anggota'"]

    B --> C{"CEK: Apakah anggota<br/>memiliki pinjaman aktif?"}

    C -- "❌ Masih ada pinjaman aktif" --> D["Sistem menampilkan:<br/>'Anggota masih memiliki<br/>pinjaman aktif yang belum lunas.<br/>Harus dilunasi terlebih dahulu<br/>sebelum bisa keluar.'"]
    D --> D1["Admin memproses<br/>pelunasan pinjaman"]
    D1 --> C

    C -- "✅ Tidak ada pinjaman aktif" --> E["Sistem menghitung total<br/>simpanan yang dikembalikan:"]

    E --> E1["📋 Rincian Pengembalian:<br/>• Simpanan Pokok: Rp 50.000<br/>• Simpanan Wajib: Rp 600.000<br/>  (12 bulan × Rp 50.000)<br/>• Simpanan Sukarela: Rp 200.000<br/>• SWP: Rp 150.000<br/>━━━━━━━━━━━━━━━━━━<br/>TOTAL: Rp 1.000.000<br/><br/>Potongan: Rp 0 (FULL)<br/>Dana dikembalikan: Rp 1.000.000"]

    E1 --> F["Admin menekan<br/>'Proses Pengembalian'"]

    F --> G["Sistem mencatat penarikan<br/>untuk setiap jenis simpanan:<br/>• TRK-2026-0001 (Pokok)<br/>• TRK-2026-0002 (Wajib)<br/>• TRK-2026-0003 (Sukarela)<br/>• TRK-2026-0004 (SWP)"]

    G --> H["Status anggota: NONAKTIF<br/>Tanggal keluar: hari ini<br/>Akun login dinonaktifkan"]

    H --> I["Log: 'Anggota NIP XXXXX<br/>keluar dari koperasi.<br/>Total dikembalikan: Rp 1.000.000'"]

    I --> SELESAI([🔴 Selesai])
```

---

## Proses Daftar Ulang (Anggota yang Pernah Keluar)

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Mantan anggota ingin<br/>bergabung kembali"]

    A --> B["Admin membuka halaman<br/>'Daftar Ulang Anggota'"]

    B --> C["Sistem menampilkan data<br/>keanggotaan sebelumnya:<br/>• Total simpanan yang diambil<br/>  saat keluar: Rp 1.000.000<br/>• Tanggal keluar: xx/xx/xxxx"]

    C --> D["Syarat daftar ulang:<br/>'Anggota harus menyetorkan<br/>kembali SELURUH simpanan<br/>yang diambil saat keluar<br/>sebesar Rp 1.000.000'"]

    D --> E{"Apakah anggota<br/>bersedia menyetor?"}
    E -- "Tidak" --> SELESAI([🔴 Selesai])

    E -- "✅ Ya" --> F["Admin memproses daftar ulang:<br/>• Status: AKTIF kembali<br/>• is_pendaftar_ulang: true<br/>• Simpanan disetor kembali<br/>• Akun login diaktifkan ulang<br/>  (PIN di-reset)"]

    F --> G["Sistem mencatat simpanan:<br/>Semua simpanan yang diambil<br/>dicatat kembali sesuai jenisnya"]

    G --> H["Log: 'Anggota NIP XXXXX<br/>daftar ulang. Total setoran<br/>kembali: Rp 1.000.000'"]

    H --> SELESAI
```

## Catatan Penting

- **Simpanan pokok dan wajib dipotong langsung dari TPP** oleh pengurus — anggota tidak perlu konfirmasi
- **SWP otomatis** saat pinjaman disetujui — tidak perlu input manual
- **Tidak ada penarikan** selama masih anggota aktif — untuk jenis simpanan apapun
- **Pengembalian FULL** tanpa potongan saat keluar — setelah semua pinjaman lunas
- **Daftar ulang** mengharuskan setoran kembali seluruh simpanan yang pernah diambil
