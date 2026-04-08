<?php

namespace App\Filament\Resources\PartnerResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Partner Code')
                    ->maxlength(20)
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Name')
                    ->maxlength(100)
                    ->required(),
                TextInput::make('city')
                    ->label('City')
                    ->maxlength(50),
                TextInput::make('country')
                    ->label('Country')
                    ->maxlength(50),
                TextInput::make('phone')
                    ->label('Phone')
                    ->maxlength(20),
                Select::make('trust_level')
                    ->label('Trust Level')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'trusted' => 'Trusted',
                    ])
                    ->default('medium'),
                Textarea::make('notes')
                    ->label('Notes'),
            ]);
    }
}
