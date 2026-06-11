<?php

namespace App\Filament\Resources\ChatRooms\Widgets;

use App\Models\ChatRoom;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChatRoomStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('Total rooms', fn () => ChatRoom::count())->icon('heroicon-o-chat-bubble-oval-left'),
            Stat::make('Active rooms', fn () => ChatRoom::where('status', 'active')->count())->icon('heroicon-o-bolt'),
        ];
    }
}
