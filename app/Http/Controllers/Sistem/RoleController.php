<?php

namespace App\Http\Controllers\Sistem;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\PermissionRegistry;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private ActivityLogService $logger
    ) {}

    /**
     * Halaman utama manajemen role.
     */
    public function index()
    {
        $roles = Role::with(['users', 'permissions'])->withCount('users', 'permissions')
            ->orderByRaw("FIELD(name, 'super_admin', 'admin', 'pimpinan') DESC, name ASC")
            ->get();

        // Non-SA roles can only be assigned operational permissions (excludes SA-only write perms)
        $permissionGroups = PermissionRegistry::assignablePermissions();

        return view('pengaturan.roles.index', compact('roles', 'permissionGroups'));
    }

    /**
     * Simpan role baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique'   => 'Nama role sudah digunakan.',
            'name.regex'    => 'Nama role hanya boleh huruf kecil dan underscore (contoh: bendahara, sekretaris_keuangan).',
        ]);

        // Filter out any SA-only permissions that might have been injected
        $safePermissions = collect($data['permissions'] ?? [])
            ->reject(fn($p) => in_array($p, PermissionRegistry::superAdminOnlyPermissions()))
            ->values()->all();

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($safePermissions);

        $this->logger->log(
            'role_created',
            "Role '{$role->name}' berhasil dibuat dengan {$role->permissions->count()} permissions.",
        );

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' berhasil dibuat.");
    }

    /**
     * Update permissions dari role yang ada.
     */
    public function update(Request $request, Role $role)
    {
        // Guard: super_admin tidak bisa diedit via UI
        if ($role->name === PermissionRegistry::ROLE_SUPER_ADMIN) {
            return back()->with('error', 'Role Super Admin tidak dapat diubah.');
        }

        $data = $request->validate([
            'display_name' => 'nullable|string|max:100',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Core roles: nama tidak bisa diubah
        // Custom roles: boleh rename
        if (!in_array($role->name, PermissionRegistry::PROTECTED_ROLES) && $request->filled('name')) {
            $request->validate([
                'name' => 'required|string|max:50|regex:/^[a-z_]+$/|unique:roles,name,' . $role->id,
            ]);
            $role->name = $request->name;
            $role->save();
        }

        // Filter out any SA-only permissions that might have been injected
        $safePermissions = collect($data['permissions'] ?? [])
            ->reject(fn($p) => in_array($p, PermissionRegistry::superAdminOnlyPermissions()))
            ->values()->all();

        $role->syncPermissions($safePermissions);

        $this->logger->log(
            'role_updated',
            "Role '{$role->name}' diperbarui. Permissions: {$role->permissions->count()}.",
        );

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' berhasil diperbarui.");
    }

    /**
     * Hapus role (hanya custom roles).
     */
    public function destroy(Role $role)
    {
        if (in_array($role->name, PermissionRegistry::PROTECTED_ROLES)) {
            return back()->with('error', "Role '{$role->name}' adalah role inti sistem dan tidak dapat dihapus.");
        }

        if ($role->users->count() > 0) {
            return back()->with('error', "Role '{$role->name}' masih memiliki {$role->users->count()} pengguna. Pindahkan pengguna terlebih dahulu.");
        }

        $nama = $role->name;
        $role->delete();

        $this->logger->log('role_deleted', "Role '{$nama}' berhasil dihapus.");

        return redirect()->route('roles.index')->with('success', "Role '{$nama}' berhasil dihapus.");
    }
}
