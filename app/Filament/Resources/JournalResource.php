<?php

namespace App\Filament\Resources;

use App\Models\JournalEntry;
use App\Filament\Resources\JournalResource\Pages;
use App\Filament\Resources\JournalResource\Tables\JournalEntriesTable;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class JournalResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    public static function table(Table $table): Table
    {
        return JournalEntriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntries::route('/'),
        ];
    }
}
