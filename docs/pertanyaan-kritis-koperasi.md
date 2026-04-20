# Daftar Pertanyaan Kritis untuk Pihak Koperasi

## Tujuan Dokumen

Dokumen ini berisi pertanyaan-pertanyaan yang **harus dijawab oleh pihak koperasi** sebelum sistem mulai dibangun. Jawaban dari pertanyaan ini akan menentukan apakah desain sistem yang sudah dirancang sudah sesuai dengan regulasi koperasi yang sebenarnya, atau perlu disesuaikan.

> ⚠️ **Penting:** Setiap pertanyaan di bawah ini ditandai dengan asumsi yang saat ini digunakan dalam desain sistem. Jika jawaban pihak koperasi **berbeda** dari asumsi, maka desain perlu diperbarui.

---

## 🔴 Prioritas 1 — WAJIB Ditanyakan (Mempengaruhi Inti Sistem)

### A. Tentang Pinjaman

| No | Pertanyaan | Asumsi Saat Ini | Dampak Jika Berbeda |
|----|-----------|----------------|---------------------|
| 1 | **Berapa persentase bunga pinjaman yang berlaku?** Apakah flat (tetap) per bulan atau menurun (anuitas)? | Bunga flat 1,5% per bulan dari pokok awal | Mengubah seluruh rumus perhitungan angsuran |
| 2 | **Berapa batas maksimal pinjaman per anggota?** Apakah ada perbedaan limit berdasarkan golongan PNS? | Rp 5.000.000 sama untuk semua anggota | Jika berbeda per golongan, perlu tabel limit tambahan |
| 3 | **Benar bahwa tenor terakhir harus lunas di bulan November (bulan ke-11)?** Dan pengajuan ditutup mulai November? | Ya, tenor maks = 11 − bulan pengajuan | Ini aturan inti — harus dipastikan benar |
| 4 | **Apakah anggota boleh punya lebih dari 1 pinjaman aktif bersamaan?** | Boleh, selama total potongan TPP ≤ 30% | Jika hanya boleh 1 pinjaman aktif, logika pengecekan berubah |
| 5 | **Apakah ada denda keterlambatan pembayaran angsuran?** Jika ya, berapa persennya? | Tidak ada denda (0%) | Perlu tambah fitur perhitungan denda |
| 6 | **Berapa persentase maksimal potongan TPP yang diperbolehkan?** | Maks 30% dari nominal TPP | Ini batasan penting untuk kelayakan pinjaman |
| 7 | **Bagaimana jika anggota tidak bisa membayar angsuran?** (misal: cuti tanpa gaji, sakit berkepanjangan, dll) Apakah ada mekanisme restrukturisasi? | Belum didesain — diasumsikan pembayaran selalu lancar via TPP | Perlu fitur penanganan pinjaman bermasalah |

### B. Tentang Simpanan

| No | Pertanyaan | Asumsi Saat Ini | Dampak Jika Berbeda |
|----|-----------|----------------|---------------------|
| 8 | **Berapa nominal simpanan pokok saat mendaftar?** | Rp 100.000 (1x saat daftar) | Mengubah nilai default di pengaturan |
| 9 | **Berapa nominal simpanan wajib per bulan?** | Rp 50.000 per bulan | Mengubah nilai default di pengaturan |
| 10 | **Apakah ada simpanan sukarela?** Apakah boleh ditarik kapan saja? | Ya, ada. Bisa ditarik kapan saja. | Jika tidak ada, modul sukarela dihapus |
| 11 | **Jika anggota keluar dari koperasi, apakah semua simpanan (pokok + wajib) dikembalikan?** Atau ada potongan? | Dikembalikan penuh setelah dipotong sisa pinjaman (jika ada) | Jika ada potongan administrasi, perlu ditambahkan |

### C. Tentang Persetujuan (Approval)

| No | Pertanyaan | Asumsi Saat Ini | Dampak Jika Berbeda |
|----|-----------|----------------|---------------------|
| 12 | **Siapa yang berwenang MENYETUJUI pinjaman?** Apakah cukup admin/bendahara saja, atau harus ada persetujuan ketua/pimpinan? | Admin (bendahara) yang menyetujui | Jika perlu 2 level approval (admin → pimpinan), alur berubah |
| 13 | **Siapa yang berjabat sebagai "Pimpinan Koperasi"?** Apakah ketua koperasi, kepala dinas, atau sekretaris? | Ketua koperasi / pejabat berwenang | Mempengaruhi hierarki akses sistem |
| 14 | **Apakah perubahan konfigurasi (bunga, limit, dll) memang perlu persetujuan pimpinan?** Atau admin bisa ubah sendiri? | Wajib persetujuan pimpinan (Maker-Checker) | Jika tidak perlu, fitur Maker-Checker bisa disederhanakan |

---

## 🟡 Prioritas 2 — Sebaiknya Ditanyakan (Mempengaruhi Fitur Pendukung)

### D. Tentang TPP dan Potongan

| No | Pertanyaan | Asumsi Saat Ini | Dampak Jika Berbeda |
|----|-----------|----------------|---------------------|
| 15 | **Apakah potongan angsuran pinjaman langsung dipotong dari TPP oleh bendahara dinas, atau anggota bayar sendiri ke koperasi?** | Dipotong langsung dari TPP oleh admin koperasi | Jika bayar sendiri, perlu fitur konfirmasi pembayaran manual |
| 16 | **Apakah nominal TPP anggota bisa berubah sewaktu-waktu?** (misal: naik golongan, mutasi jabatan) | Bisa diupdate oleh admin kapan saja | Perlu dicatat apakah ada history perubahan TPP |
| 17 | **Apakah simpanan wajib juga dipotong dari TPP, atau anggota setor sendiri?** | Belum ditentukan secara spesifik | Mempengaruhi alur pencatatan simpanan wajib |

### E. Tentang Periode dan Dana

| No | Pertanyaan | Asumsi Saat Ini | Dampak Jika Berbeda |
|----|-----------|----------------|---------------------|
| 18 | **Berapa total dana yang dialokasikan untuk pinjaman per tahun?** | Rp 100.000.000 (contoh) | Untuk pengaturan default di sistem |
| 19 | **Apakah periode pinjaman selalu Januari–Desember, atau bisa berbeda?** | Januari–Desember (1 tahun kalender) | Jika beda (misal: April–Maret), logika bulan perlu disesuaikan |
| 20 | **Jika dana periode habis sebelum akhir tahun, apakah bisa ditambah (top-up)?** | Tidak bisa — harus menunggu periode berikutnya | Jika bisa, perlu fitur penambahan dana tengah periode |
| 21 | **Apakah pinjaman yang belum lunas di akhir periode (bulan 11) bisa dilanjutkan ke periode berikutnya?** | Tidak — harus lunas di bulan 11 | Jika bisa rollover, perlu mekanisme migrasi pinjaman |

### F. Tentang Keanggotaan

| No | Pertanyaan | Asumsi Saat Ini | Dampak Jika Berbeda |
|----|-----------|----------------|---------------------|
| 22 | **Siapa saja yang BOLEH menjadi anggota koperasi?** Apakah semua pegawai dinas, atau hanya PNS? Bagaimana dengan honorer dan tenaga kontrak? | Semua karyawan/PNS Dinas PUPR | Mempengaruhi validasi data anggota |
| 23 | **Apakah anggota yang sudah keluar bisa mendaftar kembali?** | Belum didesain — diasumsikan tidak | Jika bisa, perlu mekanisme reaktivasi akun |
| 24 | **Berapa jumlah anggota koperasi saat ini?** Dan potensi pertumbuhan? | 20–50 orang (asumsi) | Mempengaruhi skala desain dan performa |

---

## 🟢 Prioritas 3 — Bagus Jika Ditanyakan (Untuk Pengembangan Lanjutan)

### G. Tentang Dana Sosial dan SHU

| No | Pertanyaan | Asumsi Saat Ini | Dampak Jika Berbeda |
|----|-----------|----------------|---------------------|
| 25 | **Apakah koperasi memiliki dana sosial untuk bantuan anggota yang mengalami musibah?** Jika ya, dari mana sumbernya? | Belum dimasukkan ke sistem | Jika ya, perlu modul dana sosial + alokasi SHU |
| 26 | **Apakah ada pembagian SHU (Sisa Hasil Usaha) ke anggota setiap akhir tahun?** Bagaimana perhitungannya? | Belum didesain | Perlu modul perhitungan dan distribusi SHU |

### H. Tentang Laporan

| No | Pertanyaan | Asumsi Saat Ini | Dampak Jika Berbeda |
|----|-----------|----------------|---------------------|
| 27 | **Format laporan apa yang biasa digunakan saat ini?** Bisa kami lihat contoh laporan manual yang sudah ada? | PDF (formal) + Excel (olah data) | Contoh laporan manual akan sangat membantu desain UI |
| 28 | **Laporan apa saja yang wajib dilaporkan ke pihak dinas atau pihak lain?** Dan berapa sering (bulanan/tahunan)? | 5 jenis laporan internal | Jika ada laporan wajib ke pihak luar, perlu format khusus |

### I. Tentang Teknis

| No | Pertanyaan | Asumsi Saat Ini | Dampak Jika Berbeda |
|----|-----------|----------------|---------------------|
| 29 | **Apakah sistem ini akan diakses dari luar kantor?** (misal: anggota cek saldo dari HP di rumah) Atau hanya dari jaringan kantor? | Belum ditentukan | Mempengaruhi keputusan hosting dan keamanan |
| 30 | **Apakah ada data anggota dan transaksi yang sudah ada sebelumnya?** (dari pencatatan manual/Excel) Yang perlu dimigrasikan ke sistem baru? | Tidak ada data lama | Jika ada, perlu fitur import data |

---

## 📋 Cara Menggunakan Dokumen Ini

1. **Cetak atau screenshot** pertanyaan Prioritas 1 (🔴) — ini WAJIB dijawab
2. **Jadwalkan pertemuan** singkat dengan pengurus koperasi (ketua/bendahara/admin)
3. **Catat jawaban** di kolom "Jawaban Koperasi" yang bisa kamu tambahkan sendiri
4. **Sampaikan ke saya** jawaban mereka, dan saya akan langsung menyesuaikan desain sistem

### Template Catatan Jawaban

```
Pertanyaan No: ___
Jawaban Koperasi: ___________________________________________
Dijawab oleh: _____________________ (jabatan)
Tanggal: ____/____/________
```

---

## Ringkasan: Berapa Banyak Asumsi yang Sudah Kita Buat?

| Kategori | Jumlah Asumsi | Status |
|----------|--------------|--------|
| Pinjaman (bunga, tenor, limit, denda) | 7 asumsi | ⚠️ Belum dikonfirmasi |
| Simpanan (jenis, nominal, penarikan) | 4 asumsi | ⚠️ Belum dikonfirmasi |
| Persetujuan (siapa approve) | 3 asumsi | ⚠️ Belum dikonfirmasi |
| TPP dan potongan | 3 asumsi | ⚠️ Belum dikonfirmasi |
| Periode dan dana | 4 asumsi | ⚠️ Belum dikonfirmasi |
| Keanggotaan | 3 asumsi | ⚠️ Belum dikonfirmasi |
| Dana sosial & SHU | 2 asumsi | ⚠️ Belum dikonfirmasi |
| Laporan | 2 asumsi | ⚠️ Belum dikonfirmasi |
| Teknis | 2 asumsi | ⚠️ Belum dikonfirmasi |
| **Total** | **30 asumsi** | **⚠️ Perlu konfirmasi** |

> 💡 **Tips:** Jangan takut bertanya banyak — lebih baik mengkonfirmasi sekarang daripada mengerjakan ulang nanti. Pengurus koperasi pasti menghargai bahwa kalian serius dan detail dalam merancang sistem mereka.
