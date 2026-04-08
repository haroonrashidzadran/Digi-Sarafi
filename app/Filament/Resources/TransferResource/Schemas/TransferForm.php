<?php

namespace App\Filament\Resources\TransferResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Transfer Code')
                    ->readOnly(),
                Select::make('sender_customer_id')
                    ->label('Sender')
                    ->relationship('senderCustomer', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('receiver_name')
                    ->label('Receiver Name')
                    ->required(),
                TextInput::make('receiver_phone')
                    ->label('Receiver Phone'),
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
                TextInput::make('fee')
                    ->label('Fee')
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }
}
