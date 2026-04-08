<?php

namespace App\Services;

use App\Models\Exchange;
use App\Models\ExchangeRate;
use App\Models\Account;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class ExchangeService
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function calculateRate(int $fromCurrencyId, int $toCurrencyId, ?float $manualRate = null): ?float
    {
        if ($manualRate) {
            return $manualRate;
        }

        return ExchangeRate::where('from_currency_id', $fromCurrencyId)
            ->where('to_currency_id', $toCurrencyId)
            ->latest()
            ->value('rate');
    }

    public function executeExchange(array $data): Exchange
    {
        return DB::transaction(function () use ($data) {
            $rate = $this->calculateRate(
                $data['from_currency_id'],
                $data['to_currency_id'],
                $data['rate'] ?? null
            );

            if (!$rate) {
                throw new \Exception('Exchange rate not found. Please set the rate first.');
            }

            $amountFrom = (float) $data['amount_from'];
            $amountTo   = round($amountFrom * $rate, 4);
            $profit     = 0; // profit is zero when rate is market rate; manual override creates profit

            $exchange = Exchange::create([
                'code'             => 'EXC' . now()->format('ymd') . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id'      => $data['customer_id'] ?? null,
                'from_currency_id' => $data['from_currency_id'],
                'to_currency_id'   => $data['to_currency_id'],
                'amount_from'      => $amountFrom,
                'amount_to'        => $amountTo,
                'rate'             => $rate,
                'profit'           => $profit,
                'profit_account_id'=> $data['profit_account_id'] ?? $this->defaultProfitAccountId(),
                'created_by_id'    => $data['created_by_id'] ?? auth()->id(),
                'status'           => 'completed',
            ]);

            $this->createExchangeJournalEntry($exchange);

            return $exchange;
        });
    }

    protected function createExchangeJournalEntry(Exchange $exchange): void
    {
        $fromCash = $this->requireCashAccount($exchange->from_currency_id);
        $toCash   = $this->requireCashAccount($exchange->to_currency_id);

        $amountFrom = $exchange->amount_from;
        $amountTo   = $exchange->amount_to;

        $lines = [
            ['account_id' => $fromCash->id, 'debit' => $amountFrom, 'credit' => 0, 'currency_id' => $exchange->from_currency_id, 'description' => 'Cash received (from currency)'],
            ['account_id' => $toCash->id, 'debit' => 0, 'credit' => $amountTo, 'currency_id' => $exchange->to_currency_id, 'description' => 'Cash paid out (to currency)'],
        ];

        $entry = $this->ledgerService->createJournalEntry([
            'description'    => "Exchange #{$exchange->code}",
            'reference_type' => Exchange::class,
            'reference_id'   => $exchange->id,
            'lines'          => $lines,
        ]);

        $this->ledgerService->postJournalEntry($entry);
        $exchange->update(['journal_entry_id' => $entry->id]);
    }

    protected function requireCashAccount(int $currencyId): Account
    {
        $account = Account::where('type', 'cash')->where('currency_id', $currencyId)->first();
        if (!$account) {
            throw new \Exception("Cash account not found for currency ID {$currencyId}.");
        }
        return $account;
    }

    protected function defaultProfitAccountId(): ?int
    {
        return Account::where('type', 'revenue')
            ->where('name', 'like', '%Exchange%')
            ->value('id')
            ?? Account::where('type', 'revenue')->value('id');
    }

    public function updateExchangeRate(int $fromCurrencyId, int $toCurrencyId, float $rate): ExchangeRate
    {
        return ExchangeRate::updateOrCreate(
            ['from_currency_id' => $fromCurrencyId, 'to_currency_id' => $toCurrencyId],
            ['rate' => $rate, 'source' => 'manual']
        );
    }

    public function getCurrencyExposure(): array
    {
        return Currency::all()->mapWithKeys(function ($currency) {
            $balance = Account::where('type', 'cash')->where('currency_id', $currency->id)
                ->get()
                ->reduce(function ($carry, $account) {
                    $b = $this->ledgerService->computeDynamicBalance($account->id);
                    return bcadd($carry, $b['balance'], 4);
                }, '0');

            return [$currency->code => ['currency_id' => $currency->id, 'balance' => $balance]];
        })->toArray();
    }

    public function getTotalProfit(?string $startDate = null, ?string $endDate = null): float
    {
        $query = Exchange::where('status', 'completed');
        if ($startDate) $query->whereDate('created_at', '>=', $startDate);
        if ($endDate)   $query->whereDate('created_at', '<=', $endDate);
        return (float) $query->sum('profit');
    }
}
