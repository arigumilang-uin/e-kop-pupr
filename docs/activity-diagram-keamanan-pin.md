# Activity Diagram — Keamanan Login (Admin & Pimpinan)

## Deskripsi

Hanya **Admin/Pengurus dan Pimpinan/Kepala** yang memiliki akun login di sistem. Anggota koperasi **tidak memiliki akun** — mereka mengakses fitur terbatas via link guest tanpa login.

## Mekanisme Keamanan

| Aspek | Detail |
|-------|--------|
| **Login** | Username + Password (bcrypt) |
| **Rate Limiting** | 5x salah → akun terkunci 30 menit |
| **Session Timeout** | 30 menit tidak aktif → auto logout |
| **Konfigurasi** | Semua batas bisa diubah via pengaturan (teknis) |

## Diagram: Login

```mermaid
flowchart TD
    MULAI([🟢 Mulai]) --> A["Pengguna membuka<br/>halaman login"]

    A --> B["Masukkan:<br/>• Username<br/>• Password"]

    B --> C{"Apakah akun<br/>sedang terkunci?"}
    C -- "🔒 Terkunci" --> C1["Pesan: 'Akun terkunci.<br/>Coba lagi dalam X menit.'"]
    C1 --> SELESAI([🔴 Selesai])

    C -- "✅ Tidak terkunci" --> D{"Username & password<br/>benar?"}

    D -- "❌ Salah" --> E["failed_login_attempts += 1"]
    E --> F{"Sudah 5x salah?"}
    F -- "Belum" --> F1["Pesan: 'Username atau password salah.<br/>Sisa percobaan: X kali.'"]
    F1 --> A
    F -- "🔒 Sudah 5x" --> G["Akun TERKUNCI 30 menit<br/>locked_until = now + 30 menit"]
    G --> G1["Log: 'Akun USERNAME terkunci<br/>karena 5x salah password'"]
    G1 --> C1

    D -- "✅ Benar" --> H["Reset failed_login_attempts = 0"]
    H --> I["Catat: last_login_at, last_login_ip"]
    I --> J["Login berhasil ✅<br/>Redirect ke dashboard"]
    J --> K["Log: 'USERNAME berhasil login'"]
    K --> SELESAI
```

## Diagram: Session Timeout

```mermaid
flowchart TD
    A["Pengguna login<br/>dan tidak aktif"] --> B{"Sudah 30 menit<br/>tidak ada aktivitas?"}
    B -- "Belum" --> A
    B -- "✅ 30 menit" --> C["Session expired<br/>Auto logout"]
    C --> D["Redirect ke login:<br/>'Sesi Anda telah berakhir.'"]
```

## Catatan

- **Tidak ada fitur ubah PIN/password untuk anggota** — karena anggota tidak punya akun
- Pengurus yang lupa password harus menghubungi admin lain atau pimpinan
- Semua aktivitas login tercatat di `log_aktivitas`
