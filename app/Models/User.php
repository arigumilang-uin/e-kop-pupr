<?php

namespace App\Models;

use App\Enums\RoleUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => RoleUser::class,
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    // === Role Checks ===

    public function isAdmin(): bool
    {
        return $this->role === RoleUser::Admin;
    }

    public function isPimpinan(): bool
    {
        return $this->role === RoleUser::Pimpinan;
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
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
