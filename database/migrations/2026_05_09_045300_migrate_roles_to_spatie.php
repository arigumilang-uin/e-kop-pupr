<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 1. Add is_active column to users table
     * 2. Migrate old enum role data to Spatie roles
     * 3. Drop the old role enum column
     */
    public function up(): void
    {
        // Step 1: Add is_active (for soft-deactivation instead of delete)
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
        });

        // Step 2: Migrate existing role data
        // We keep track of old roles before dropping the column
        $users = DB::table('users')->select('id', 'role')->get();

        // Step 3: Create Spatie roles first via the model
        $roleMap = [];
        foreach (['super_admin', 'admin', 'pimpinan'] as $roleName) {
            $existing = DB::table('roles')->where('name', $roleName)->where('guard_name', 'web')->first();
            if (!$existing) {
                $roleId = DB::table('roles')->insertGetId([
                    'name'       => $roleName,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $roleMap[$roleName] = $roleId;
            } else {
                $roleMap[$roleName] = $existing->id;
            }
        }

        // Step 4: Assign Spatie roles based on old enum values
        foreach ($users as $user) {
            // User ID 1 becomes super_admin
            if ($user->id === 1) {
                $targetRole = 'super_admin';
            } else {
                $targetRole = $user->role; // 'admin' or 'pimpinan'
            }

            $roleId = $roleMap[$targetRole] ?? $roleMap['admin'];

            // Insert into Spatie's model_has_roles pivot
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id'    => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id'   => $user->id,
                ],
            );
        }

        // Step 5: Drop old enum column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        // Re-add old role enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'pimpinan'])->default('admin')->after('last_login_ip');
        });

        // Attempt to restore role data from Spatie tables
        $modelRoles = DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('model_has_roles.model_id', 'roles.name')
            ->get();

        foreach ($modelRoles as $mr) {
            $role = in_array($mr->name, ['admin', 'pimpinan']) ? $mr->name : 'admin';
            DB::table('users')->where('id', $mr->model_id)->update(['role' => $role]);
        }

        // Drop is_active
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
