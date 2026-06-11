<?php

namespace App\Filament\Resources\ChatRooms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChatRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user1_id'),
                TextInput::make('user2_id'),
                TextInput::make('status'),
            ]);
    }
}
