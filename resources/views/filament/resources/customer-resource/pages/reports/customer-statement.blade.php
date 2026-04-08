<x-filament-panels::page>
    <div class="mb-6">
        <form wire:submit.prevent="generateStatement">
            {{ $this->form }}
            <div class="mt-4">
                <x-filament::button type="submit" color="primary">
                    Generate Statement
                </x-filament::button>
            </div>
        </form>
    </div>

    @if($this->record)
        <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
            <h2 class="text-lg font-semibold">{{ $this->record->name }}</h2>
            <p class="text-sm text-gray-600">Code: {{ $this->record->code }}</p>
            <p class="text-sm text-gray-600">Period: {{ $data['start_date'] }} to {{ $data['end_date'] }}</p>
        </div>

        <x-filament::table>
            <x-slot name="header">
                <x-filament-tables::row>
                    <x-filament-tables::cell class="px-4 py-3">Date</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3">Description</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3 text-right">Debit</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3 text-right">Credit</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3">Currency</x-filament-tables::cell>
                </x-filament-tables::row>
            </x-slot>
            @php
                $ledgers = \App\Models\CustomerLedger::where('customer_id', $this->record->id)
                    ->whereHas('journalEntry', function ($q) {
                        $q->where('status', 'approved');
                        if (!empty($this->data['start_date'])) {
                            $q->where('date', '>=', $this->data['start_date']);
                        }
                        if (!empty($this->data['end_date'])) {
                            $q->where('date', '<=', $this->data['end_date']);
                        }
                    })
                    ->with(['journalEntry', 'currency'])
                    ->orderBy('id')
                    ->get();
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @foreach($ledgers as $ledger)
                @php
                    $totalDebit += $ledger->debit;
                    $totalCredit += $ledger->credit;
                @endphp
                <x-filament-tables::row>
                    <x-filament-tables::cell class="px-4 py-3">{{ $ledger->journalEntry?->date?->format('Y-m-d') }}</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3">{{ $ledger->description }}</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3 text-right">{{ number_format($ledger->debit, 4) }}</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3 text-right">{{ number_format($ledger->credit, 4) }}</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3">{{ $ledger->currency?->code }}</x-filament-tables::cell>
                </x-filament-tables::row>
            @endforeach
            <x-slot name="footer">
                <x-filament-tables::row>
                    <x-filament-tables::cell class="px-4 py-3 font-semibold" colspan="2">Totals</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3 text-right font-semibold">{{ number_format($totalDebit, 4) }}</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3 text-right font-semibold">{{ number_format($totalCredit, 4) }}</x-filament-tables::cell>
                    <x-filament-tables::cell class="px-4 py-3"></x-filament-tables::cell>
                </x-filament-tables::row>
            </x-slot>
        </x-filament::table>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>
