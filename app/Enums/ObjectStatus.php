<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabels;
use Filament\Support\Contracts\HasLabel;

enum ObjectStatus: string implements HasLabel
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
