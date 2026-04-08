<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div><span class="font-semibold">Date:</span> {{ $entry->date->format('Y-m-d') }}</div>
        <div><span class="font-semibold">Status:</span> {{ ucfirst($entry->status) }}</div>
        <div class="col-span-2"><span class="font-semibold">Description:</span> {{ $entry->description }}</div>
    </div>

    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="bg-gray-100 dark:bg-gray-700">
                <th class="text-left p-2 border border-gray-200 dark:border-gray-600">Account</th>
                <th class="text-left p-2 border border-gray-200 dark:border-gray-600">Description</th>
                <th class="text-right p-2 border border-gray-200 dark:border-gray-600">Currency</th>
                <th class="text-right p-2 border border-gray-200 dark:border-gray-600">Debit</th>
                <th class="text-right p-2 border border-gray-200 dark:border-gray-600">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entry->lines as $line)
            <tr class="border-b border-gray-200 dark:border-gray-600">
                <td class="p-2 border border-gray-200 dark:border-gray-600">
                    {{ $line->account->code }} — {{ $line->account->name }}
                </td>
                <td class="p-2 border border-gray-200 dark:border-gray-600">{{ $line->description }}</td>
                <td class="p-2 text-right border border-gray-200 dark:border-gray-600">{{ $line->currency->code }}</td>
                <td class="p-2 text-right border border-gray-200 dark:border-gray-600">
                    {{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}
                </td>
                <td class="p-2 text-right border border-gray-200 dark:border-gray-600">
                    {{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-semibold bg-gray-50 dark:bg-gray-800">
                <td colspan="3" class="p-2 border border-gray-200 dark:border-gray-600 text-right">Totals</td>
                <td class="p-2 text-right border border-gray-200 dark:border-gray-600">
                    {{ number_format($entry->lines->sum('debit'), 2) }}
                </td>
                <td class="p-2 text-right border border-gray-200 dark:border-gray-600">
                    {{ number_format($entry->lines->sum('credit'), 2) }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
