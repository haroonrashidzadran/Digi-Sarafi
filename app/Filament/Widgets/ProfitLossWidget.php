<?php

namespace App\Filament\Widgets;

use App\Services\LedgerService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProfitLossWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $pl = app(LedgerService::class)->getProfitAndLoss(
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        return [
            Stat::make('Revenue (Month)', number_format($pl['revenue'], 2))
                ->color('success'),

            Stat::make('Expenses (Month)', number_format($pl['expenses'], 2))
                ->color('danger'),

            Stat::make('Net Profit (Month)', number_format($pl['profit'], 2))
                ->color($pl['profit'] >= 0 ? 'success' : 'danger'),
        ];
    }
}
