<?php

namespace App\Filament\Resources\TransferResource\Tables;

use App\Models\Transfer;
use App\Services\TransferService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class TransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('senderCustomer.name')
                    ->label('Sender')
                    ->searchable(),
                TextColumn::make('receiver_name')
                    ->label('Receiver'),
                TextColumn::make('partner.name')
                    ->label('Partner'),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency.code')
                    ->label('Currency'),
                TextColumn::make('fee')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'pending'  => 'warning',
                        'sent'     => 'info',
                        'paid'     => 'success',
                        'settled'  => 'gray',
                        'cancelled'=> 'danger',
                        default    => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                        'settled' => 'Settled',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('currency_id')
                    ->relationship('currency', 'code')
                    ->label('Currency'),
                Filter::make('created_today')
                    ->query(fn ($q) => $q->whereDate('created_at', today()))
                    ->label('Today'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('mark_sent')
                        ->label('Mark as Sent')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->visible(fn (Transfer $r) => $r->status === 'pending')
                        ->requiresConfirmation()
                        ->action(function (Transfer $record) {
                            app(TransferService::class)->markAsSent($record);
                            Notification::make()->title('Transfer marked as sent.')->success()->send();
                        }),

                    Action::make('mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Transfer $r) => $r->status === 'sent')
                        ->action(function (Transfer $record) {
                            Notification::make()->title('Use OTP to mark as paid')->warning()->send();
                        }),

                    Action::make('settle')
                        ->label('Settle')
                        ->icon('heroicon-o-banknotes')
                        ->color('gray')
                        ->visible(fn (Transfer $r) => $r->status === 'paid')
                        ->requiresConfirmation()
                        ->action(function (Transfer $record) {
                            app(TransferService::class)->settleTransfer($record);
                            Notification::make()->title('Transfer settled.')->success()->send();
                        }),

                    Action::make('cancel')
                        ->label('Cancel')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->visible(fn (Transfer $r) => in_array($r->status, ['pending', 'sent']))
                        ->requiresConfirmation()
                        ->action(function (Transfer $record) {
                            app(TransferService::class)->cancelTransfer($record);
                            Notification::make()->title('Transfer cancelled.')->warning()->send();
                        }),

                    Action::make('show_otp')
                        ->label('Show OTP')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->visible(fn (Transfer $r) => in_array($r->status, ['pending', 'sent']))
                        ->action(function (Transfer $record) {
                            Notification::make()->title("OTP: {$record->otp_code}")->info()->send();
                        }),
                ]),
            ]);
    }
}
