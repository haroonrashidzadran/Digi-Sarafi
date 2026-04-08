<?php

namespace App\Filament\Resources\JournalResource\Tables;

use App\Models\JournalEntry;
use App\Services\LedgerService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class JournalEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'draft'    => 'warning',
                        'approved' => 'success',
                        'reversed' => 'danger',
                        default    => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Created By'),
                TextColumn::make('approvedBy.name')
                    ->label('Approved By'),
                TextColumn::make('reference_type')
                    ->label('Ref Type')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '—'),
                TextColumn::make('reference_id')
                    ->label('Ref ID'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'    => 'Draft',
                        'approved' => 'Approved',
                        'reversed' => 'Reversed',
                    ]),
                Filter::make('date_today')
                    ->query(fn ($q) => $q->whereDate('date', today()))
                    ->label('Today'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (JournalEntry $r) => $r->status === 'draft')
                    ->requiresConfirmation()
                    ->action(function (JournalEntry $record) {
                        app(LedgerService::class)->postJournalEntry($record);
                        Notification::make()->title('Journal entry approved.')->success()->send();
                    }),

                Action::make('reverse')
                    ->label('Reverse')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn (JournalEntry $r) => $r->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function (JournalEntry $record) {
                        app(LedgerService::class)->reverseJournalEntry($record);
                        Notification::make()->title('Journal entry reversed.')->warning()->send();
                    }),

                Action::make('view_lines')
                    ->label('View Lines')
                    ->icon('heroicon-o-ellipsis-horizontal')
                    ->color('gray')
                    ->modalHeading(fn (JournalEntry $r) => "Journal Entry #{$r->id} — Lines")
                    ->modalContent(fn (JournalEntry $record) => view(
                        'filament.journal-lines',
                        ['entry' => $record->load('lines.account', 'lines.currency')]
                    ))
                    ->modalSubmitAction(false),
            ]);
    }
}
