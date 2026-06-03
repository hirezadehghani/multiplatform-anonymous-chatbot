<?php

namespace App\Filament\Resources\Bots\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('platform')
                    ->required(),
                TextInput::make('token')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('settings'),
            ]);
    }
}
