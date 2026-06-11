<?php

namespace App\Filament\Resources\ChatRooms;

use App\Filament\Resources\ChatRooms\Pages\CreateChatRoom;
use App\Filament\Resources\ChatRooms\Pages\EditChatRoom;
use App\Filament\Resources\ChatRooms\Pages\ListChatRooms;
use App\Filament\Resources\ChatRooms\Schemas\ChatRoomForm;
use App\Filament\Resources\ChatRooms\Tables\ChatRoomsTable;
use App\Filament\Resources\ChatRooms\Widgets\ChatRoomStats;
use App\Models\ChatRoom;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ChatRoomResource extends Resource
{
    protected static ?string $model = ChatRoom::class;

    public static function form(Schema $schema): Schema
    {
        return ChatRoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatRoomsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatRooms::route('/'),
            'create' => CreateChatRoom::route('/create'),
            'edit' => EditChatRoom::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [ChatRoomStats::class];
    }
}
