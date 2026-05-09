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

        // Note: Assignment of Super Admin and Admin roles to users
        // is handled in DatabaseSeeder.php after user creation.
    }
}
