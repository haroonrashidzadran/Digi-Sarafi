<?php

namespace App\Filament\Resources\CurrencyResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Currency Code')
                    ->maxlength(3)
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Name')
                    ->maxlength(50)
                    ->required(),
                TextInput::make('symbol')
                    ->label('Symbol')
                    ->maxlength(10)
                    ->required(),
                Toggle::make('is_base')
                    ->label('Base Currency')
                    ->default(false),
            ]);
    }
}
