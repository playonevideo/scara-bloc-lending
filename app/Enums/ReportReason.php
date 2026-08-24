<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabels;
use Filament\Support\Contracts\HasLabel;

enum ReportReason: string implements HasLabel
{
    use HasLabels;

    case InappropriateObject = 'inappropriate_object';
    case InappropriateDescription = 'inappropriate_description';
    case Spam = 'spam';
    case AbusiveBehavior = 'abusive_behavior';
    case InappropriateMessage = 'inappropriate_message';
    case ProblematicUser = 'problematic_user';

    public static function labels(): array
    {
        return [
            self::InappropriateObject->value => 'Obiect nepotrivit',
            self::InappropriateDescription->value => 'Descriere nepotrivită',
            self::Spam->value => 'Spam',
            self::AbusiveBehavior->value => 'Comportament abuziv',
            self::InappropriateMessage->value => 'Mesaj nepotrivit',
            self::ProblematicUser->value => 'Utilizator problematic',
        ];
    }
}
