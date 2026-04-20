<?php

namespace App\Enums;

enum RoleUser: string
{
    case Admin = 'admin';
    case Pimpinan = 'pimpinan';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin/Pengurus',
            self::Pimpinan => 'Pimpinan/Kepala',
        };
    }
}
