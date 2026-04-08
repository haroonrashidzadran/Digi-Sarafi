<?php

namespace App\Filament\Resources\CashSessionResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CashSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cashier_id')
                    ->label('Cashier')
                    ->relationship('cashier', 'name')
                    ->required(),
                TextInput::make('opening_balance')
                    ->label('Opening Balance')
                    ->numeric()
                    ->default(0),
                TextInput::make('closing_balance')
                    ->label('Closing Balance')
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ])
                    ->default('open'),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }
}
