<?php

namespace App\Filament\Widgets;

use App\Services\LedgerService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashPositionWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $positions = app(LedgerService::class)->computeCashPosition();
        $stats = [];

        foreach ($positions['accounts'] as $code => $data) {
            $stats[] = Stat::make("Cash {$data['currency']}", number_format($data['balance'], 2))
                ->description("Account: {$code}")
                ->color($data['balance'] >= 0 ? 'success' : 'danger');
        }

        return $stats;
    }
}
