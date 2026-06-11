<?php

namespace App\Filament\Resources\Messages;

use App\Filament\Resources\Messages\Pages\ListMessages;
use App\Filament\Resources\Messages\Pages\ViewMessage;
use App\Filament\Resources\Messages\Tables\MessagesTable;
use App\Filament\Resources\Messages\Widgets\MessageStats;
use App\Models\Message;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    public static function table(Table $table): Table
    {
        return MessagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMessages::route('/'),
            'view' => ViewMessage::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [MessageStats::class];
    }
}
