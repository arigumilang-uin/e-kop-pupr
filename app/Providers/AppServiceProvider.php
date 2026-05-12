<?php

namespace App\Providers;

use App\Services\PermissionRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureSuperAdminGate();

        // Deteksi N+1 query lebih dini di environment development
        Model::preventLazyLoading(! app()->isProduction());

        // View Composer: share sidebar data tanpa query di Blade
        View::composer('layouts.partials.sidebar-nav', function ($view) {
            $view->with(
                'pendingVoidCount',
                \App\Models\VoidRequest::where('status', 'menunggu')->count()
            );
        });

        // Gunakan Host bawaan request, karena ini pasti lolos dari Docker
        $host = request()->getHost();

        // Jika URL mengandung trycloudflare.com (atau domain ngrok dll)
        if (str_contains($host, 'trycloudflare.com')) {
            // Paksa Root URL menggunakan HTTPS dan host Cloudflare
            URL::forceRootUrl('https://' . $host);
            
            // Paksa generator link (termasuk Vite) untuk menggunakan HTTPS
            URL::forceScheme('https');
        }
    }

    /**
     * Gate::before enforces RBAC boundaries:
     *
     * 1. Super Admin gets implicit access to system management permissions ONLY.
     *    They do NOT get operational permissions (anggota, simpanan, pinjaman, etc.)
     *
     * 2. Non-Super Admin users are explicitly DENIED super-admin-only write
     *    permissions (pengaturan.edit, user.create/edit/deactivate, role.create/edit/delete)
     *    even if they are somehow assigned via DB manipulation.
     */
    private function configureSuperAdminGate(): void
    {
        Gate::before(function ($user, string $ability) {
            // 1. Permission implications (e.g. anggota.profile → anggota.view)
            if ($this->checkPermissionImplication($user, $ability)) {
                return true;
            }

            if ($user->hasRole(PermissionRegistry::ROLE_SUPER_ADMIN)) {
                // SA implicitly gets system management permissions
                if (in_array($ability, PermissionRegistry::superAdminPermissions())) {
                    return true;
                }
                // For operational permissions, fall through to normal check
                // (SA won't have these by default unless explicitly assigned)
                return null;
            }

            // Non-SA: block super-admin-only write permissions at Gate level
            if (in_array($ability, PermissionRegistry::superAdminOnlyPermissions())) {
                return false;
            }

            return null; // Normal permission check
        });
    }

    /**
     * Check if user has a permission that implies the requested ability.
     *
     * Example: User has 'anggota.profile' → automatically passes 'anggota.view' check.
     */
    private function checkPermissionImplication($user, string $ability): bool
    {
        foreach (PermissionRegistry::permissionImplications() as $parent => $implied) {
            if (in_array($ability, $implied) && $user->hasPermissionTo($parent)) {
                return true;
            }
        }

        return false;
    }
}