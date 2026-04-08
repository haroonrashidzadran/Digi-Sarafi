<?php

namespace App\Filament\Resources\CurrencyResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class CurrenciesTable
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
                TextColumn::make('symbol')
                    ->label('Symbol'),
                BooleanColumn::make('is_base')
                    ->label('Base'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->filters([
                Filter::make('base')
                    ->query(fn ($query) => $query->where('is_base', true))
                    ->label('Base Currency'),
            ]);
    }
}
