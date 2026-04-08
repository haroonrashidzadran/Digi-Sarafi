<?php

namespace App\Filament\Resources\CustomerResource\Tables;

use App\Models\Customer;
use App\Models\CustomerLedger;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CustomersTable
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
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone'),
                TextColumn::make('preferredCurrency.code')
                    ->label('Currency'),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'blocked' => 'Blocked',
                    ]),
            ])
            ->actions([
                Action::make('view_ledger')
                    ->label('View Ledger')
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('info')
                    ->modalHeading(fn (Customer $r) => "Ledger: {$r->name}")
                    ->modalContent(function (Customer $record) {
                        $ledgers = CustomerLedger::where('customer_id', $record->id)
                            ->with(['journalEntry', 'account', 'currency'])
                            ->orderBy('id', 'desc')
                            ->limit(50)
                            ->get();
                        
                        $html = '<table class="w-full text-sm"><thead><tr class="bg-gray-100"><th class="p-2">Date</th><th class="p-2">Account</th><th class="p-2">Debit</th><th class="p-2">Credit</th><th class="p-2">Description</th></tr></thead><tbody>';
                        foreach ($ledgers as $ledger) {
                            $html .= '<tr class="border-b">';
                            $html .= '<td class="p-2">' . ($ledger->journalEntry?->date?->format('Y-m-d') ?? '-') . '</td>';
                            $html .= '<td class="p-2">' . ($ledger->account?->name ?? '-') . '</td>';
                            $html .= '<td class="p-2 text-right">' . number_format($ledger->debit, 4) . '</td>';
                            $html .= '<td class="p-2 text-right">' . number_format($ledger->credit, 4) . '</td>';
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
