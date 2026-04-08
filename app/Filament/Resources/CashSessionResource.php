<?php

namespace App\Filament\Resources;

use App\Models\CashSession;
use App\Filament\Resources\CashSessionResource\Pages;
use App\Filament\Resources\CashSessionResource\Schemas\CashSessionForm;
use App\Filament\Resources\CashSessionResource\Tables\CashSessionsTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CashSessionResource extends Resource
{
    protected static ?string $model = CashSession::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    public static function form(Schema $schema): Schema
    {
        return CashSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashSessionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashSessions::route('/'),
            'edit' => Pages\EditCashSession::route('/{record}/edit'),
        ];
    }
}
