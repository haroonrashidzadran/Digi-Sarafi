<?php

namespace App\Filament\Resources\ExchangeRateResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExchangeRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('from_currency_id')
                    ->label('From Currency')
                    ->relationship('fromCurrency', 'code')
                    ->required(),
                Select::make('to_currency_id')
                    ->label('To Currency')
                    ->relationship('toCurrency', 'code')
                    ->required(),
                TextInput::make('rate')
                    ->label('Rate')
                    ->numeric()
                    ->required()
                    ->minValue(0.000001),
                TextInput::make('source')
                    ->label('Source')
                    ->maxlength(20)
                    ->default('manual'),
            ]);
    }
}
