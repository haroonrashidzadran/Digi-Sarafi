<?php

namespace App\Filament\Resources\CustomerResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Customer Code')
                    ->maxlength(20)
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Name')
                    ->maxlength(100)
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxlength(100),
                TextInput::make('phone')
                    ->label('Phone')
                    ->maxlength(20),
                Select::make('preferred_currency_id')
                    ->label('Preferred Currency')
                    ->relationship('preferredCurrency', 'code'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'blocked' => 'Blocked',
                    ])
                    ->default('active'),
                Textarea::make('notes')
                    ->label('Notes'),
            ]);
    }
}
