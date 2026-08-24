<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabels;

enum ObjectCondition: string
{
    use HasLabels;

    case New = 'new';
    case VeryGood = 'very_good';
    case Good = 'good';
    case Acceptable = 'acceptable';
    case NeedsRepair = 'needs_repair';

    public static function labels(): array
    {
        return [
            self::New->value => 'Nou',
            self::VeryGood->value => 'Foarte bună',
            self::Good->value => 'Bună',
            self::Acceptable->value => 'Acceptabilă',
            self::NeedsRepair->value => 'Necesită reparații',
        ];
    }
}
