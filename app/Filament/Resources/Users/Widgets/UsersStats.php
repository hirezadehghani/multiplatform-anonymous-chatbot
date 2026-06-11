<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('Total users', fn () => User::count())->icon('heroicon-o-users'),
            Stat::make('Searching', fn () => User::where('status', 'searching')->count())->icon('heroicon-o-magnifying-glass'),
            Stat::make('Chatting', fn () => User::where('status', 'chatting')->count())->icon('heroicon-o-chat-bubble-oval-left'),
        ];
    }
}
