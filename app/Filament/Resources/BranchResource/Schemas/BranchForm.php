<?php

namespace App\Filament\Resources\BranchResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Branch Code')
                    ->maxlength(20)
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Name')
                    ->maxlength(100)
                    ->required(),
                TextInput::make('address')
                    ->label('Address')
                    ->maxlength(200),
                TextInput::make('phone')
                    ->label('Phone')
                    ->maxlength(20),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
