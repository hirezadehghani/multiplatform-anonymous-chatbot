<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('display_name')->required(),
                TextInput::make('email'),
                TextInput::make('uuid')->disabled(),
                Toggle::make('profile_completed'),
            ]);
    }
}
