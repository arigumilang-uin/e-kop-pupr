<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\PengaturanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(
        private PengaturanService $pengaturan,
        private ActivityLogService $logger,
    ) {}

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
    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        // User tidak ditemukan
        if (!$user) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau password salah.']);
        }

        // Cek akun terkunci
        if ($user->isLocked()) {
            $sisaMenit = now()->diffInMinutes($user->locked_until, false);
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => "Akun terkunci. Coba lagi dalam {$sisaMenit} menit."]);
        }

        // Cek password
        if (!Hash::check($request->password, $user->password)) {
            return $this->handleGagalLogin($user);
        }

        // ✅ Login berhasil
        return $this->handleBerhasilLogin($request, $user);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $this->logger->logLogout($user->id, $user->nama);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // === Private Methods ===

    private function handleGagalLogin(User $user)
    {
        $maksPercobaan = $this->pengaturan->maksPercobaanLogin();
        $durasiKunci = $this->pengaturan->durasiKunciAkunMenit();

        $user->increment('failed_login_attempts');

        // Kunci akun jika sudah melebihi batas
        if ($user->failed_login_attempts >= $maksPercobaan) {
            $user->update(['locked_until' => now()->addMinutes($durasiKunci)]);
            $this->logger->logAkunTerkunci($user->id, $user->username, $maksPercobaan);

            return back()
                ->withInput(request()->only('username'))
                ->withErrors(['username' => "Akun terkunci selama {$durasiKunci} menit karena terlalu banyak percobaan gagal."]);
        }

        $sisa = $maksPercobaan - $user->failed_login_attempts;
        return back()
            ->withInput(request()->only('username'))
            ->withErrors(['username' => "Username atau password salah. Sisa percobaan: {$sisa}."]);
    }

    private function handleBerhasilLogin(Request $request, User $user)
    {
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $this->logger->logLogin($user->id, $user->nama, $user->role->value);

        return redirect()->intended(route('dashboard'));
    }
}
