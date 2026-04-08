<?php

namespace App\Filament\Resources\AccountResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('currency.code')
                    ->label('Currency'),
                BooleanColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'cash' => 'Cash',
                        'bank' => 'Bank',
                        'customer' => 'Customer',
                        'partner' => 'Partner',
                    ]),
                Filter::make('active')
                    ->query(fn ($query) => $query->where('is_active', true))
                    ->label('Active Only'),
            ]);
    }
}
