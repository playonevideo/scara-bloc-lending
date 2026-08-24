<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabels;

enum ObjectStatus: string implements \Filament\Support\Contracts\HasLabel
{
    use HasLabels;

    case Available = 'available';
    case Reserved = 'reserved';
    case Borrowed = 'borrowed';
    case Inactive = 'inactive';

    public static function labels(): array
    {
        return [
            self::Available->value => 'Disponibil',
            self::Reserved->value => 'Rezervat',
            self::Borrowed->value => 'Împrumutat',
            self::Inactive->value => 'Inactiv',
        ];
    }
}
