<?php

namespace App\Filament\Resources\Messages\Widgets;

use App\Models\Message;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MessageStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('Total messages', fn () => Message::count())->icon('heroicon-o-chat-bubble-left-right'),
            Stat::make('Today', fn () => Message::whereDate('created_at', now()->toDateString())->count())->icon('heroicon-o-calendar-today'),
        ];
    }
}
