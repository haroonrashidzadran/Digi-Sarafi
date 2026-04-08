<?php

namespace App\Filament\Resources;

use App\Models\ExchangeRate;
use App\Filament\Resources\ExchangeRateResource\Pages;
use App\Filament\Resources\ExchangeRateResource\Schemas\ExchangeRateForm;
use App\Filament\Resources\ExchangeRateResource\Tables\ExchangeRatesTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    public static function form(Schema $schema): Schema
    {
        return ExchangeRateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExchangeRatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchangeRates::route('/'),
        ];
    }
}
