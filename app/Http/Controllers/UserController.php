<?php

namespace App\Http\Controllers;

use App\Enums\RoleUser;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Anggota;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private ActivityLogService $logger
    ) {}

    public function index(Request $request)
    {
        $query = User::query()->orderByDesc('created_at');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20)->withQueryString();

        // Preload anggota data for NIP-linked users
        $nips = $users->pluck('nip')->filter()->unique()->toArray();
        $anggotaMap = Anggota::whereIn('nip', $nips)->pluck('nama', 'nip');

        return view('pengaturan.users.index', compact('users', 'anggotaMap'));
    }

    public function create()
    {
        $roles = RoleUser::cases();
        return view('pengaturan.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Nama otomatis dari data anggota berdasarkan NIP
        $data['nama'] = Anggota::where('nip', $data['nip'])->value('nama');

        $user = User::create($data);

        $this->logger->log(
            'user_created',
            "Akun pengguna baru '{$user->nama}' ({$user->role->label()}) berhasil dibuat.",
        );

        return redirect()->route('users.index')->with('success', "Akun {$user->nama} berhasil dibuat.");
    }

    public function edit(User $user)
    {
        $roles = RoleUser::cases();
        $anggotaNama = null;

        if ($user->nip) {
            $anggotaNama = Anggota::where('nip', $user->nip)->value('nama');
        }

        return view('pengaturan.users.edit', compact('user', 'roles', 'anggotaNama'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // Nama otomatis dari data anggota berdasarkan NIP
        $data['nama'] = Anggota::where('nip', $data['nip'])->value('nama');

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        $this->logger->log(
            'user_updated',
            "Akun pengguna '{$user->nama}' ({$user->role->label()}) berhasil diperbarui.",
        );

        return redirect()->route('users.index')->with('success', "Akun {$user->nama} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        // Guard: tidak bisa hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $nama = $user->nama;
        $user->delete();

        $this->logger->log(
            'user_deleted',
            "Akun pengguna '{$nama}' berhasil dihapus.",
        );

        return redirect()->route('users.index')->with('success', "Akun {$nama} berhasil dihapus.");
    }
}
