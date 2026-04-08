<?php

namespace App\Filament\Resources;

use App\Models\Branch;
use App\Filament\Resources\BranchResource\Pages;
use App\Filament\Resources\BranchResource\Schemas\BranchForm;
use App\Filament\Resources\BranchResource\Tables\BranchesTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    public static function form(Schema $schema): Schema
    {
        return BranchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
        ];
    }
}
