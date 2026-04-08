<?php

namespace App\Filament\Resources\CashSessionResource\Pages;

use App\Filament\Resources\CashSessionResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditCashSession extends EditRecord
{
    protected static string $resource = CashSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
