<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabels;

enum ReportStatus: string
{
    use HasLabels;

    case New = 'new';
    case InReview = 'in_review';
    case Resolved = 'resolved';
    case Dismissed = 'dismissed';

    public static function labels(): array
    {
        return [
            self::New->value => 'Nou',
            self::InReview->value => 'În analiză',
            self::Resolved->value => 'Rezolvat',
            self::Dismissed->value => 'Respins',
        ];
    }
}
