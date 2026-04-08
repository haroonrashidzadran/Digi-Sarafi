<?php

namespace App\Filament\Resources\ExchangeResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExchangeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name')
                    ->searchable(),
                Select::make('from_currency_id')
                    ->label('From Currency')
                    ->relationship('fromCurrency', 'code')
                    ->required(),
                Select::make('to_currency_id')
                    ->label('To Currency')
                    ->relationship('toCurrency', 'code')
                    ->required(),
                TextInput::make('amount_from')
                    ->label('Amount Given')
                    ->numeric()
                    ->required()
                    ->minValue(0.01),
                TextInput::make('rate')
                    ->label('Exchange Rate')
                    ->numeric()
                    ->required()
                    ->minValue(0.000001),
                TextInput::make('amount_to')
                    ->label('Amount Received')
                    ->numeric()
                    ->readOnly(),
                TextInput::make('profit')
                    ->label('Profit')
                    ->numeric()
                    ->readOnly(),
            ]);
    }
}
