<?php

namespace App\Filament\Resources\CashSessionResource\Tables;

use App\Models\CashSession;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CashSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('cashier.name')
                    ->label('Cashier')
                    ->searchable(),
                TextColumn::make('opened_at')
                    ->label('Opened At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->label('Closed At')
                    ->dateTime(),
                TextColumn::make('opening_balance')
                    ->numeric(),
                TextColumn::make('closing_balance')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'open'   => 'success',
                        'closed' => 'gray',
                        default  => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ]),
            ])
            ->actions([
                Action::make('close_session')
                    ->label('Close Session')
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->visible(fn (CashSession $r) => $r->status === 'open')
                    ->action(function (CashSession $record) {
                        Notification::make()->title('Close session form')->warning()->send();
                    }),
            ]);
    }
}
