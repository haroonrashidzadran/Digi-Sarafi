<?php

namespace App\Filament\Resources\ExchangeResource\Tables;

use App\Models\Exchange;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ExchangesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('fromCurrency.code')
                    ->label('From'),
                TextColumn::make('amount_from')
                    ->label('Amount In')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('toCurrency.code')
                    ->label('To'),
                TextColumn::make('amount_to')
                    ->label('Amount Out')
                    ->numeric(),
                TextColumn::make('rate')
                    ->label('Rate'),
                TextColumn::make('profit')
                    ->label('Profit')
                    ->numeric()
                    ->color('success'),
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
                SelectFilter::make('from_currency_id')
                    ->relationship('fromCurrency', 'code')
                    ->label('From Currency'),
                SelectFilter::make('to_currency_id')
                    ->relationship('toCurrency', 'code')
                    ->label('To Currency'),
                Filter::make('created_today')
                    ->query(fn ($q) => $q->whereDate('created_at', today()))
                    ->label('Today'),
            ])
            ->actions([
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Exchange $r) => $r->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Exchange $record) {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()->title('Exchange cancelled.')->warning()->send();
                    }),
            ]);
    }
}
