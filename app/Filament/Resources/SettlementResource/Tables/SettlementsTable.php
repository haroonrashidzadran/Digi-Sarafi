<?php

namespace App\Filament\Resources\SettlementResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class SettlementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label('Partner')
                    ->searchable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency.code')
                    ->label('Currency'),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'completed' => 'success',
                        'pending'   => 'warning',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'cash' => 'Cash',
                        'bank' => 'Bank',
                        'adjustment' => 'Adjustment',
                    ]),
                SelectFilter::make('currency_id')
                    ->relationship('currency', 'code')
                    ->label('Currency'),
                Filter::make('created_today')
                    ->query(fn ($q) => $q->whereDate('created_at', today()))
                    ->label('Today'),
            ]);
    }
}
