<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabels;

enum Role: string implements \Filament\Support\Contracts\HasLabel
{
    use HasLabels;

    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Resident = 'resident';

    public static function labels(): array
    {
        return [
            self::SuperAdmin->value => 'Super Administrator',
            self::Admin->value => 'Administrator',
            self::Resident->value => 'Locatar',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin], true);
    }
}
