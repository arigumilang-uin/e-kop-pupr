<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Anggota;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\PermissionRegistry;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

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
            $query->role($request->role); // Spatie scope
        }

        $users = $query->paginate(20)->withQueryString();

        // Preload anggota data for NIP-linked users
        $nips = $users->pluck('nip')->filter()->unique()->toArray();
        $anggotaMap = Anggota::whereIn('nip', $nips)->pluck('nama', 'nip');

        $roles = $this->getAssignableRoles();

        return view('pengaturan.users.index', compact('users', 'anggotaMap', 'roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $roleName = $data['role'];
        unset($data['role']);

        // Nama: dari anggota jika NIP ada, dari input 'nama' jika kosong
        if (!empty($data['nip'])) {
            $data['nama'] = Anggota::where('nip', $data['nip'])->value('nama');
        }

        $user = User::create($data);
        $user->assignRole($roleName);

        $this->logger->log(
            'user_created',
            "Akun pengguna baru '{$user->nama}' (role: {$roleName}) berhasil dibuat.",
        );

        return redirect()->route('users.index')->with('success', "Akun {$user->nama} berhasil dibuat.");
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $roleName = $data['role'];
        unset($data['role']);

        // Nama: dari anggota jika NIP ada, dari input 'nama' jika kosong
        if (!empty($data['nip'])) {
            $data['nama'] = Anggota::where('nip', $data['nip'])->value('nama');
        }

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$roleName]);

        $this->logger->log(
            'user_updated',
            "Akun pengguna '{$user->nama}' (role: {$roleName}) berhasil diperbarui.",
        );

        return redirect()->route('users.index')->with('success', "Akun {$user->nama} berhasil diperbarui.");
    }

    /**
     * Toggle user active status (deactivate/reactivate).
     */
    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if ($user->hasRole(PermissionRegistry::ROLE_SUPER_ADMIN) && !auth()->user()->hasRole(PermissionRegistry::ROLE_SUPER_ADMIN)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menonaktifkan Super Admin.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        $this->logger->log(
            'user_status_changed',
            "Akun pengguna '{$user->nama}' berhasil {$status}.",
        );

        return redirect()->route('users.index')->with('success', "Akun {$user->nama} berhasil {$status}.");
    }

    /**
     * Get roles that the current user can assign.
     * Only Super Admin can assign the super_admin role.
     */
    private function getAssignableRoles()
    {
        $query = Role::query()->orderBy('name');

        if (!auth()->user()->hasRole(PermissionRegistry::ROLE_SUPER_ADMIN)) {
            $query->where('name', '!=', PermissionRegistry::ROLE_SUPER_ADMIN);
        }

        return $query->get();
    }
}
