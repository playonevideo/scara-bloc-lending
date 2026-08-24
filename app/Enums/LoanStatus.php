<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabels;

enum LoanStatus: string
{
    use HasLabels;

    case Requested = 'requested';
    case Accepted = 'accepted';
    case Borrowed = 'borrowed';
    case Returned = 'returned';
    case Completed = 'completed';
    case Refused = 'refused';
    case Cancelled = 'cancelled';
    case Overdue = 'overdue';

    public static function labels(): array
    {
        return [
            self::Requested->value => 'Solicitat',
            self::Accepted->value => 'Acceptat',
            self::Borrowed->value => 'Împrumutat',
            self::Returned->value => 'Returnat',
            self::Completed->value => 'Finalizat',
            self::Refused->value => 'Refuzat',
            self::Cancelled->value => 'Anulat',
            self::Overdue->value => 'Întârziat',
        ];
    }

    /**
     * Statuses that mark the loan as actively holding the object.
     */
    public function blocksObject(): bool
    {
        return in_array($this, [self::Accepted, self::Borrowed, self::Overdue], true);
    }
}
