<?php

namespace App\Filament\Resources\PartnerResource\Tables;

use App\Models\Partner;
use App\Models\PartnerLedger;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('city')
                    ->label('City'),
                TextColumn::make('country')
                    ->label('Country'),
                TextColumn::make('phone')
                    ->label('Phone'),
                TextColumn::make('trust_level')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('trust_level')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'trusted' => 'Trusted',
                    ]),
            ])
            ->actions([
                Action::make('view_ledger')
                    ->label('View Ledger')
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('info')
                    ->modalHeading(fn (Partner $r) => "Ledger: {$r->name}")
                    ->modalContent(function (Partner $record) {
                        $ledgers = PartnerLedger::where('partner_id', $record->id)
                            ->with(['journalEntry', 'currency'])
                            ->orderBy('id', 'desc')
                            ->limit(50)
                            ->get();
                        
                        $html = '<table class="w-full text-sm"><thead><tr class="bg-gray-100"><th class="p-2">Date</th><th class="p-2">Direction</th><th class="p-2">Amount</th><th class="p-2">Currency</th><th class="p-2">Description</th></tr></thead><tbody>';
                        foreach ($ledgers as $ledger) {
                            $html .= '<tr class="border-b">';
                            $html .= '<td class="p-2">' . ($ledger->journalEntry?->date?->format('Y-m-d') ?? '-') . '</td>';
                            $html .= '<td class="p-2">' . ($ledger->direction === 'debit' ? 'DR' : 'CR') . '</td>';
                            $html .= '<td class="p-2 text-right">' . number_format($ledger->amount, 4) . '</td>';
                            $html .= '<td class="p-2">' . ($ledger->currency?->code ?? '-') . '</td>';
                            $html .= '<td class="p-2">' . ($ledger->description ?? '-') . '</td>';
                            $html .= '</tr>';
                        }
                        $html .= '</tbody></table>';
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false),
            ]);
    }
}
