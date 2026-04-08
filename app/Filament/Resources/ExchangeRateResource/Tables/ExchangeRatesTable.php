<?php

namespace App\Filament\Resources\ExchangeRateResource\Tables;

use App\Models\ExchangeRate;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExchangeRatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fromCurrency.code')
                    ->label('From')
                    ->sortable(),
                TextColumn::make('toCurrency.code')
                    ->label('To')
                    ->sortable(),
                TextColumn::make('rate')
                    ->label('Rate')
                    ->numeric(6)
                    ->sortable(),
                TextColumn::make('source')
                    ->label('Source'),
                TextColumn::make('created_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('from_currency_id')
                    ->relationship('fromCurrency', 'code')
                    ->label('From Currency'),
                SelectFilter::make('to_currency_id')
                    ->relationship('toCurrency', 'code')
                    ->label('To Currency'),
            ])
            ->actions([
                Action::make('update_rate')
                    ->label('Update Rate')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->action(function (ExchangeRate $record) {
                        Notification::make()->title('Rate update form')->warning()->send();
                    }),
            ]);
    }
}
