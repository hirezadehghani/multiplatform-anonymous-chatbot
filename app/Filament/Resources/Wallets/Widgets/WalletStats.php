<?php

namespace App\Filament\Resources\Wallets\Widgets;

use App\Models\Wallet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WalletStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('Total wallets', fn () => Wallet::count())->icon('heroicon-o-wallet'),
            Stat::make('Total balance', fn () => Wallet::sum('balance'))->icon('heroicon-o-banknotes'),
        ];
    }
}
