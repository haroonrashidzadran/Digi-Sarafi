<?php

namespace App\Filament\Resources\AccountResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Account Code')
                    ->maxlength(20)
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Name')
                    ->maxlength(100)
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options([
                        'cash' => 'Cash',
                        'bank' => 'Bank',
                        'customer' => 'Customer',
                        'partner' => 'Partner',
                        'income' => 'Income',
                        'expense' => 'Expense',
                        'liability' => 'Liability',
                        'equity' => 'Equity',
                        'revenue' => 'Revenue',
                        'cost_of_sales' => 'Cost of Sales',
                    ])
                    ->required(),
                Select::make('currency_id')
                    ->label('Currency')
                    ->relationship('currency', 'code')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Textarea::make('description')
                    ->label('Description'),
            ]);
    }
}
