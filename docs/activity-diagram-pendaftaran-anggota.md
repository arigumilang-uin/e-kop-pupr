# Activity Diagram — Pendaftaran Anggota Baru

## Deskripsi

Proses pendaftaran anggota baru ke koperasi. Dilakukan oleh **Admin** setelah menerima data dari calon anggota. Setelah berhasil, sistem otomatis: mencatat **simpanan pokok Rp 50.000**, membuat **akun login dengan PIN default**, dan menghasilkan **nomor referensi** transaksi.

## Diagram

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Admin membuka halaman<br/>'Tambah Anggota Baru'"]
    A --> B["Admin mengisi formulir:<br/>• NIP<br/>• Nama Lengkap<br/>• Golongan PNS<br/>• Jabatan<br/>• Bidang/Bagian<br/>• Nomor HP<br/>• Alamat"]
    B --> C["Admin menekan 'Simpan'"]

    C --> D{"Apakah data valid?"}
    D -- "❌ Tidak" --> E["Pesan kesalahan"]
    E --> B

    D -- "✅ Valid" --> F{"Apakah NIP sudah<br/>terdaftar?"}
    F -- "⚠️ Sudah" --> F1{"Apakah NIP ini<br/>pernah keluar koperasi?"}
    F1 -- "Ya — mantan anggota" --> F1a["Arahkan ke proses<br/>'Daftar Ulang Anggota'<br/>(lihat diagram simpanan)"]
    F1a --> SELESAI([🔴 Selesai])
    F1 -- "Tidak — masih aktif" --> F2["Pesan: 'NIP sudah terdaftar<br/>sebagai anggota aktif.'"]
    F2 --> B

    F -- "✅ Belum" --> G["Sistem menyimpan anggota baru<br/>Status: AKTIF"]
    G --> H["Sistem membuat akun login:<br/>• Username: NIP<br/>• PIN default: 6 digit acak (bcrypt)<br/>• Status: WAJIB GANTI PIN"]
    H --> I["Sistem mencatat simpanan pokok:<br/>• No. Ref: SIM-2026-XXXX<br/>• Nominal: Rp 50.000<br/>• Jenis: POKOK"]
    I --> J["Log: 'Admin mendaftarkan<br/>anggota baru NIP XXXXX'"]
    J --> K["Konfirmasi berhasil +<br/>PIN default ditampilkan<br/>(hanya 1x)"]
    K --> SELESAI
```

## Keamanan PIN

| Aspek | Detail |
|-------|--------|
| PIN Default | 6 digit angka acak (bcrypt) |
| Wajib Ganti | Saat login pertama |
| Tidak Boleh | Sama dengan NIP, berurutan (123456), berulang (111111) |
| Batas Salah | 5x → kunci 30 menit |

## Catatan

- **Simpanan pokok Rp 50.000** dicatat otomatis (nominal bisa diubah via pengaturan)
- PIN default **hanya ditampilkan sekali** — admin tidak bisa melihat lagi setelah halaman ditutup
- Jika NIP pernah keluar, sistem mengarahkan ke **proses daftar ulang** (harus setor kembali simpanan)
