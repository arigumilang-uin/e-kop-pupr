<?php

namespace Database\Seeders;

use App\Services\PermissionRegistry;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================
        // 1. Seed all permissions
        // ========================
        $allPermissions = PermissionRegistry::allPermissionNames();

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name'       => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info("✅ " . count($allPermissions) . " permissions seeded.");

        // ========================
        // 2. Seed core roles
        // ========================
        $superAdmin = Role::firstOrCreate(['name' => PermissionRegistry::ROLE_SUPER_ADMIN, 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => PermissionRegistry::ROLE_ADMIN,       'guard_name' => 'web']);
        $pimpinan   = Role::firstOrCreate(['name' => PermissionRegistry::ROLE_PIMPINAN,    'guard_name' => 'web']);

        // ========================
        // 3. Sync permissions to roles
        // ========================
        $superAdmin->syncPermissions(PermissionRegistry::superAdminPermissions());
        $admin->syncPermissions(PermissionRegistry::adminPermissions());
        $pimpinan->syncPermissions(PermissionRegistry::pimpinanPermissions());

        $this->command->info("✅ Permissions synced: Super Admin ({$superAdmin->permissions->count()}), Admin ({$admin->permissions->count()}), Pimpinan ({$pimpinan->permissions->count()}).");

        // ========================
        // 4. Assign Super Admin role to User ID=1
        // ========================
        $coreUser = User::find(1);

        if ($coreUser) {
            if ($coreUser->username !== 'core-admin') {
                $coreUser->update(['username' => 'core-admin']);
                $this->command->info("✅ User ID=1 username updated to 'core-admin'.");
            }

            if (!$coreUser->hasRole(PermissionRegistry::ROLE_SUPER_ADMIN)) {
                $coreUser->syncRoles([PermissionRegistry::ROLE_SUPER_ADMIN]);
            }

            $this->command->info("✅ User '{$coreUser->nama}' (ID=1) assigned as Super Admin.");
        } else {
            $this->command->warn("⚠️ User ID=1 not found. Please manually assign Super Admin after seeding.");
        }
    }
}
