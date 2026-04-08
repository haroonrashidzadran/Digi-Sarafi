<?php

namespace App\Filament\Resources;

use App\Models\Settlement;
use App\Filament\Resources\SettlementResource\Pages;
use App\Filament\Resources\SettlementResource\Schemas\SettlementForm;
use App\Filament\Resources\SettlementResource\Tables\SettlementsTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SettlementResource extends Resource
{
    protected static ?string $model = Settlement::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Partners';

    public static function form(Schema $schema): Schema
    {
        return SettlementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettlementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettlements::route('/'),
        ];
    }
}
