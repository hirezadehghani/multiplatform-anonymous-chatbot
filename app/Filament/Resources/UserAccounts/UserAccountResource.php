<?php

namespace App\Filament\Resources\UserAccounts;

use App\Filament\Resources\UserAccounts\Pages\CreateUserAccount;
use App\Filament\Resources\UserAccounts\Pages\EditUserAccount;
use App\Filament\Resources\UserAccounts\Pages\ListUserAccounts;
use App\Filament\Resources\UserAccounts\Schemas\UserAccountForm;
use App\Filament\Resources\UserAccounts\Tables\UserAccountsTable;
use App\Models\UserAccount;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserAccountResource extends Resource
{
    protected static ?string $model = UserAccount::class;

    public static function form(Schema $schema): Schema
    {
        return UserAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserAccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserAccounts::route('/'),
            'create' => CreateUserAccount::route('/create'),
            'edit' => EditUserAccount::route('/{record}/edit'),
        ];
    }
}
