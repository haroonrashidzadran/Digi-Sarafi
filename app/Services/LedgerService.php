<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Currency;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\CustomerLedger;
use App\Models\PartnerLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LedgerService
{
    public function createJournalEntry(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            $entry = JournalEntry::create([
                'date' => $data['date'] ?? now()->toDateString(),
                'description' => $data['description'],
                'status' => 'draft',
                'created_by_id' => $data['created_by_id'] ?? auth()->id(),
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
            ]);

            foreach ($data['lines'] as $lineData) {
                $entry->lines()->create([
                    'account_id' => $lineData['account_id'],
                    'debit' => $lineData['debit'] ?? 0,
                    'credit' => $lineData['credit'] ?? 0,
                    'currency_id' => $lineData['currency_id'],
                    'description' => $lineData['description'] ?? null,
                ]);
            }

            if (!$entry->isBalanced()) {
                throw new \Exception('Journal entry is not balanced per currency. Debit must equal credit for each currency.');
            }

            return $entry;
        });
    }

    public function validateDoubleEntry(array $lines): bool
    {
        $byCurrency = collect($lines)->groupBy('currency_id');

        foreach ($byCurrency as $currencyId => $currencyLines) {
            $debit  = $currencyLines->sum('debit');
            $credit = $currencyLines->sum('credit');
            if (bccomp((string)$debit, (string)$credit, 4) !== 0) {
                return false;
            }
        }

        return true;
    }

    public function postJournalEntry(JournalEntry $entry, ?int $approvedById = null): JournalEntry
    {
        return DB::transaction(function () use ($entry, $approvedById) {
            if ($entry->status !== 'draft') {
                throw new \Exception('Only draft journal entries can be posted.');
            }

            if (!$entry->isBalanced()) {
                throw new \Exception('Journal entry must be balanced before posting.');
            }

            $entry->update([
                'status' => 'approved',
                'approved_by_id' => $approvedById ?? auth()->id(),
            ]);

            if ($entry->reference_type && $entry->reference_id) {
                $this->attachToReference($entry);
            }

            return $entry;
        });
    }

    public function reverseJournalEntry(JournalEntry $entry, ?int $reversedById = null): JournalEntry
    {
        return DB::transaction(function () use ($entry, $reversedById) {
            if ($entry->status !== 'approved') {
                throw new \Exception('Only approved journal entries can be reversed.');
            }

            $reversedEntry = JournalEntry::create([
                'date' => now()->toDateString(),
                'description' => 'Reversal of: ' . $entry->description,
                'status' => 'approved',
                'created_by_id' => $reversedById ?? auth()->id(),
                'reference_type' => $entry->reference_type,
                'reference_id' => $entry->reference_id,
            ]);

            foreach ($entry->lines as $line) {
                $reversedEntry->lines()->create([
                    'account_id' => $line->account_id,
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                    'currency_id' => $line->currency_id,
                    'description' => 'Reversal: ' . ($line->description ?? ''),
                ]);
            }

            $entry->update(['status' => 'reversed']);

            return $reversedEntry;
        });
    }

    public function attachToReference(JournalEntry $entry): void
    {
        if (!$entry->reference_type || !$entry->reference_id) {
            return;
        }

        $modelClass = $entry->reference_type;
        
        if (class_exists($modelClass) && method_exists($modelClass, 'journalEntry')) {
            $model = $modelClass::find($entry->reference_id);
            if ($model && $model->journalEntry_id !== $entry->id) {
                $model->update(['journal_entry_id' => $entry->id]);
            }
        }
    }

    public function computeDynamicBalance(int $accountId, ?string $currencyId = null): array
    {
        $query = JournalLine::where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'approved');
            });

        if ($currencyId) {
            $query->where('currency_id', $currencyId);
        }

        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');

        $account = Account::find($accountId);
        $isDebitNormal = in_array($account->type, ['cash', 'bank', 'customer', 'asset', 'cost_of_sales']);
        
        $balance = $isDebitNormal 
            ? bcsub($totalDebit, $totalCredit, 4)
            : bcsub($totalCredit, $totalDebit, 4);

        return [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'balance' => $balance,
            'is_debit_normal' => $isDebitNormal,
        ];
    }

    public function computePartnerExposure(int $partnerId, ?string $currencyId = null): array
    {
        $query = PartnerLedger::where('partner_id', $partnerId)
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'approved');
            });

        if ($currencyId) {
            $query->where('currency_id', $currencyId);
        }

        $debitTotal = $query->where('direction', 'debit')->sum('amount');
        $creditTotal = $query->where('direction', 'credit')->sum('amount');

        return [
            'debit' => $debitTotal,
            'credit' => $creditTotal,
            'balance' => bcsub($creditTotal, $debitTotal, 4),
        ];
    }

    public function computeCashPosition(?string $currencyId = null): array
    {
        $cashAccounts = Account::where('type', 'cash')->get();
        
        $positions = [];
        $total = 0;

        foreach ($cashAccounts as $account) {
            $balance = $this->computeDynamicBalance($account->id, $currencyId);
            $positions[$account->code] = [
                'account_id' => $account->id,
                'currency' => $account->currency->code,
                'balance' => $balance['balance'],
            ];
            $total = bcadd($total, $balance['balance'], 4);
        }

        return [
            'accounts' => $positions,
            'total' => $total,
            'currency' => $currencyId ?? Currency::where('is_base', true)->first()?->code,
        ];
    }

    public function computeCustomerBalance(int $customerId, ?string $currencyId = null): array
    {
        $query = CustomerLedger::where('customer_id', $customerId)
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'approved');
            });

        if ($currencyId) {
            $query->where('currency_id', $currencyId);
        }

        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');

        return [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'balance' => bcsub($totalCredit, $totalDebit, 4),
        ];
    }

    public function getAccountTransactions(int $accountId, ?string $startDate = null, ?string $endDate = null)
    {
        $query = JournalLine::where('account_id', $accountId)
            ->with(['journalEntry', 'currency'])
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'approved');
            })
            ->orderBy('journal_entry_id');

        if ($startDate) {
            $query->whereHas('journalEntry', function ($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('journalEntry', function ($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            });
        }

        return $query->get();
    }

    public function getTrialBalance(?string $date = null): array
    {
        $query = JournalEntry::where('status', 'approved');
        
        if ($date) {
            $query->where('date', '<=', $date);
        }

        $entryIds = $query->pluck('id');
        
        $accounts = Account::active()->get();
        $trialBalance = [];

        foreach ($accounts as $account) {
            $totals = JournalLine::whereIn('journal_entry_id', $entryIds)
                ->where('account_id', $account->id)
                ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                ->first();

            if ($totals->debit > 0 || $totals->credit > 0) {
                $isDebitNormal = in_array($account->type, ['cash', 'bank', 'customer', 'asset', 'cost_of_sales']);
                
                $trialBalance[] = [
                    'account_id' => $account->id,
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'account_type' => $account->type,
                    'debit' => $totals->debit,
                    'credit' => $totals->credit,
                    'balance' => $isDebitNormal 
                        ? bcsub($totals->debit, $totals->credit, 4)
                        : bcsub($totals->credit, $totals->debit, 4),
                ];
            }
        }

        return $trialBalance;
    }

    public function getProfitAndLoss(?string $startDate = null, ?string $endDate = null): array
    {
        $query = JournalEntry::where('status', 'approved');
        
        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $entryIds = $query->pluck('id');
        
        $revenueAccounts = Account::ofType('revenue')->active()->get();
        $expenseAccounts = Account::ofType(['expense', 'cost_of_sales'])->active()->get();

        $revenue = 0;
        foreach ($revenueAccounts as $account) {
            $total = JournalLine::whereIn('journal_entry_id', $entryIds)
                ->where('account_id', $account->id)
                ->sum('credit');
            $revenue = bcadd($revenue, $total, 4);
        }

        $expenses = 0;
        foreach ($expenseAccounts as $account) {
            $total = JournalLine::whereIn('journal_entry_id', $entryIds)
                ->where('account_id', $account->id)
                ->sum('debit');
            $expenses = bcadd($expenses, $total, 4);
        }

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => bcsub($revenue, $expenses, 4),
        ];
    }
}