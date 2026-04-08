<?php

namespace App\Filament\Resources;

use App\Models\Exchange;
use App\Filament\Resources\ExchangeResource\Pages;
use App\Filament\Resources\ExchangeResource\Schemas\ExchangeForm;
use App\Filament\Resources\ExchangeResource\Tables\ExchangesTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ExchangeResource extends Resource
{
    protected static ?string $model = Exchange::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    public static function form(Schema $schema): Schema
    {
        return ExchangeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExchangesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchanges::route('/'),
            'edit' => Pages\EditExchange::route('/{record}/edit'),
        ];
    }
}
