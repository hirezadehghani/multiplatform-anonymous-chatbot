<?php

namespace App\Filament\Resources\Bots;

use App\Filament\Resources\Bots\Pages\CreateBot;
use App\Filament\Resources\Bots\Pages\EditBot;
use App\Filament\Resources\Bots\Pages\ListBots;
use App\Filament\Resources\Bots\Schemas\BotForm;
use App\Filament\Resources\Bots\Tables\BotsTable;
use App\Models\Bot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BotResource extends Resource
{
    protected static ?string $model = Bot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BotsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBots::route('/'),
            'create' => CreateBot::route('/create'),
            'edit' => EditBot::route('/{record}/edit'),
        ];
    }
}
