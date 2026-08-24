<?php

namespace App\Filament\Widgets;

use App\Enums\Role;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Message;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Locatari', User::query()->where('role', Role::Resident->value)->count())
                ->description('Conturi active')
                ->icon('heroicon-o-users'),
            Stat::make('Obiecte', Item::query()->count())
                ->description(Item::query()->available()->published()->count().' disponibile')
                ->icon('heroicon-o-squares-2x2'),
            Stat::make('Împrumuturi active', Loan::query()->active()->count())
                ->icon('heroicon-o-arrows-right-left'),
            Stat::make('Cereri în așteptare', Loan::query()->where('status', 'requested')->count())
                ->icon('heroicon-o-clock'),
            Stat::make('Mesaje', Message::query()->count())
                ->icon('heroicon-o-chat-bubble-left-right'),
        ];
    }
}
