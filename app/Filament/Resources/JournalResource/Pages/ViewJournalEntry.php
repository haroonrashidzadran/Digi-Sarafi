<?php

namespace App\Filament\Resources\JournalResource\Pages;

use App\Filament\Resources\JournalResource;
use Filament\Resources\Pages\ListRecords;

// View page replaced by modal action on the list page.
class ViewJournalEntry extends ListRecords
{
    protected static string $resource = JournalResource::class;
}
