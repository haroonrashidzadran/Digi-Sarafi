<?php

namespace App\Filament\Resources\SettlementResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettlementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->readOnly(),
                Select::make('partner_id')
                    ->label('Partner')
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required()
                    ->minValue(0.01),
                Select::make('currency_id')
                    ->label('Currency')
                    ->relationship('currency', 'code')
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options([
                        'cash' => 'Cash',
                        'bank' => 'Bank',
                        'adjustment' => 'Adjustment',
                    ])
                    ->default('cash'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('completed'),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
            ]);
    }
}
