<?php

namespace App\Filament\Resources\UserAccounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id'),
                TextInput::make('platform'),
                TextInput::make('platform_user_id'),
                TextInput::make('username'),
                Toggle::make('is_primary'),
            ]);
    }
}
