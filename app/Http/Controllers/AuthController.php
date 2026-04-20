<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login dengan rate limiting.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        // User tidak ditemukan
        if (!$user) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau password salah.']);
        }

        // Cek apakah akun terkunci
        $maksPercobaan = (int) Pengaturan::getValue('maks_percobaan_login', 5);
        $durasiKunci = (int) Pengaturan::getValue('durasi_kunci_akun_menit', 30);

        if ($user->isLocked()) {
            $sisaMenit = now()->diffInMinutes($user->locked_until, false);
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => "Akun terkunci. Coba lagi dalam {$sisaMenit} menit."]);
        }

        // Cek password
        if (!Hash::check($request->password, $user->password)) {
            $user->increment('failed_login_attempts');

            // Kunci akun jika sudah melebihi batas
            if ($user->failed_login_attempts >= $maksPercobaan) {
                $user->update([
                    'locked_until' => now()->addMinutes($durasiKunci),
                ]);

                LogAktivitas::create([
                    'user_id' => $user->id,
                    'aktivitas' => 'akun_terkunci',
                    'deskripsi' => "Akun {$user->username} terkunci karena {$maksPercobaan}x salah password.",
                    'ip_address' => $request->ip(),
                ]);

                return back()
                    ->withInput($request->only('username'))
                    ->withErrors(['username' => "Akun terkunci selama {$durasiKunci} menit karena terlalu banyak percobaan gagal."]);
            }

            $sisa = $maksPercobaan - $user->failed_login_attempts;
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => "Username atau password salah. Sisa percobaan: {$sisa}."]);
        }

        // Login berhasil — reset counter & catat
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        LogAktivitas::create([
            'user_id' => $user->id,
            'aktivitas' => 'login',
            'deskripsi' => "{$user->nama} ({$user->role}) berhasil login.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            LogAktivitas::create([
                'user_id' => $user->id,
                'aktivitas' => 'logout',
                'deskripsi' => "{$user->nama} logout.",
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
