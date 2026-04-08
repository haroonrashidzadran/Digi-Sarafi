<?php

namespace App\Filament\Widgets;

use App\Models\Transfer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransferStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Transfers Today', Transfer::whereDate('created_at', today())->count())
                ->description('All statuses')
                ->color('primary'),

            Stat::make('Pending', Transfer::where('status', 'pending')->count())
                ->color('warning'),

            Stat::make('Paid (Unsettled)', Transfer::where('status', 'paid')->count())
                ->color('info'),
        ];
    }
}
