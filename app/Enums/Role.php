<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabels;
use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    use HasLabels;

    case Admin = 'admin';
    case Resident = 'resident';

    public static function labels(): array
    {
        return [
            self::Admin->value => 'Administrator',
            self::Resident->value => 'Locatar',
        ];
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
