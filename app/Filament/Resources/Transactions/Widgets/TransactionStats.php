<?php

namespace App\Filament\Resources\Transactions\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Stat::make('Total transactions', fn () => Transaction::count())->icon('heroicon-o-receipt-refund'),
            Stat::make('Total volume', fn () => Transaction::sum('amount'))->icon('heroicon-o-currency-dollar'),
        ];
    }
}
