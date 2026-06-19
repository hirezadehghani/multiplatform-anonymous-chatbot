<?php

namespace App\Filament\Widgets;

use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\Wallet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('Users', fn () => User::count())->icon('heroicon-o-users'),
            Stat::make('User Accounts', fn () => UserAccount::count())->icon('heroicon-o-user-circle'),
            Stat::make('Chat Rooms', fn () => ChatRoom::count())->icon('heroicon-o-chat-bubble-left-right'),
            Stat::make('Messages', fn () => Message::count())->icon('heroicon-o-chat-bubble-left-right'),
            Stat::make('Wallet Inventory', fn () => Wallet::sum('balance'))->icon('heroicon-o-banknotes'),
        ];
    }
}
