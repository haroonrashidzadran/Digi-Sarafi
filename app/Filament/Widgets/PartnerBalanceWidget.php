<?php

namespace App\Filament\Widgets;

use App\Models\Partner;
use App\Services\LedgerService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PartnerBalanceWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Partner::query())
            ->columns([
                TextColumn::make('code')->label('Code'),
                TextColumn::make('name')->label('Partner'),
                TextColumn::make('city')->label('City'),
                TextColumn::make('trust_level')->badge()->label('Trust'),
                TextColumn::make('balance')
                    ->label('Net Balance (AFN)')
                    ->state(function (Partner $record): string {
                        $ledger = app(LedgerService::class)->computePartnerExposure($record->id);
                        return number_format($ledger['balance'], 2);
                    })
                    ->color(fn (Partner $record): string => app(LedgerService::class)->computePartnerExposure($record->id)['balance'] >= 0 ? 'success' : 'danger'),
            ])
            ->heading('Partner Balances');
    }
}
