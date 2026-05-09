<?php

namespace App\Models;

use App\Services\PermissionRegistry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'nip',
        'is_active',
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'      => 'hashed',
            'is_active'     => 'boolean',
            'locked_until'  => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    // === Role Checks (Convenience Methods) ===

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(PermissionRegistry::ROLE_SUPER_ADMIN);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(PermissionRegistry::ROLE_ADMIN);
    }

    public function isPimpinan(): bool
    {
        return $this->hasRole(PermissionRegistry::ROLE_PIMPINAN);
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    // === Scopes ===

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // === Anggota Link ===

    public function anggota(): ?Anggota
    {
        if (empty($this->nip)) {
            return null;
        }

        return Anggota::where('nip', $this->nip)->first();
    }

    public function hasAnggota(): bool
    {
        return !empty($this->nip) && Anggota::where('nip', $this->nip)->exists();
    }

    // === Scopes ===

    public function scopePengurusAktif($query)
    {
        $nipAnggotaAktif = Anggota::where('status', 'aktif')->pluck('nip');

        return $query->whereNotNull('nip')
                     ->whereIn('nip', $nipAnggotaAktif);
    }

    // === Relationships ===

    public function pinjamanDisetujui(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'approved_by');
    }

    public function periodeDibuka(): HasMany
    {
        return $this->hasMany(PeriodePinjaman::class, 'dibuka_oleh');
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class);
    }

    public function perubahanDiajukan(): HasMany
    {
        return $this->hasMany(PerubahanPengaturan::class, 'diajukan_oleh');
    }

    public function perubahanDiputuskan(): HasMany
    {
        return $this->hasMany(PerubahanPengaturan::class, 'diputuskan_oleh');
    }
}
