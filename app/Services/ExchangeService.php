<?php

namespace App\Services;

use App\Models\Exchange;
use App\Models\ExchangeRate;
use App\Models\Account;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class ExchangeService
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function calculateRate(int $fromCurrencyId, int $toCurrencyId, ?float $manualRate = null): ?float
    {
        if ($manualRate) {
            return $manualRate;
        }

        $exchangeRate = ExchangeRate::where('from_currency_id', $fromCurrencyId)
            ->where('to_currency_id', $toCurrencyId)
            ->latest()
            ->first();

        return $exchangeRate ? $exchangeRate->rate : null;
    }

    public function calculateProfit(float $amountFrom, float $amountTo, float $rate): float
    {
        $expectedAmount = $amountFrom * $rate;
        return $amountTo - $expectedAmount;
    }

    public function calculateOutputAmount(float $inputAmount, float $rate): float
    {
        return $inputAmount * $rate;
    }

    public function executeExchange(array $data): Exchange
    {
        return DB::transaction(function () use ($data) {
            $fromCurrency = Currency::find($data['from_currency_id']);
            $toCurrency = Currency::find($data['to_currency_id']);

            $rate = $this->calculateRate(
                $data['from_currency_id'],
                $data['to_currency_id'],
                $data['rate'] ?? null
            );

            if (!$rate) {
                throw new \Exception('Exchange rate not found. Please set the rate first.');
            }

            $amountFrom = $data['amount_from'];
            $amountTo = $this->calculateOutputAmount($amountFrom, $rate);
            $profit = $this->calculateProfit($amountFrom, $amountTo, $rate);

            $code = $this->generateExchangeCode();

            $exchange = Exchange::create([
                'code' => $code,
                'customer_id' => $data['customer_id'] ?? null,
                'from_currency_id' => $data['from_currency_id'],
                'to_currency_id' => $data['to_currency_id'],
                'amount_from' => $amountFrom,
                'amount_to' => $amountTo,
                'rate' => $rate,
                'profit' => $profit,
                'profit_account_id' => $data['profit_account_id'] ?? $this->getDefaultProfitAccount()?->id,
                'created_by_id' => $data['created_by_id'] ?? auth()->id(),
                'status' => 'completed',
            ]);

            $this->createExchangeJournalEntry($exchange);

            return $exchange;
        });
    }

    protected function generateExchangeCode(): string
    {
        $prefix = 'EXC';
        $date = now()->format('ymd');
        $random = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        return "{$prefix}{$date}{$random}";
    }

    protected function getDefaultProfitAccount(): ?Account
    {
        return Account::where('type', 'revenue')
            ->where('name', 'like', '%Exchange Profit%')
            ->first();
    }

    protected function createExchangeJournalEntry(Exchange $exchange): void
    {
        $fromCashAccount = Account::where('type', 'cash')
            ->where('currency_id', $exchange->from_currency_id)
            ->first();

        $toCashAccount = Account::where('type', 'cash')
            ->where('currency_id', $exchange->to_currency_id)
            ->first();

        if (!$fromCashAccount) {
            throw new \Exception('Cash account not found for currency: ' . $exchange->fromCurrency->code);
        }

        if (!$toCashAccount) {
            throw new \Exception('Cash account not found for currency: ' . $exchange->toCurrency->code);
        }

        $lines = [];

        $lines[] = [
            'account_id' => $fromCashAccount->id,
            'debit' => 0,
            'credit' => $exchange->amount_from,
            'currency_id' => $exchange->from_currency_id,
            'description' => 'Cash received from customer',
        ];

        $lines[] = [
            'account_id' => $toCashAccount->id,
            'debit' => $exchange->amount_to,
            'credit' => 0,
            'currency_id' => $exchange->to_currency_id,
            'description' => 'Cash given to customer',
        ];

        if ($exchange->profit > 0 && $exchange->profit_account_id) {
            $lines[] = [
                'account_id' => $exchange->profit_account_id,
                'debit' => 0,
                'credit' => $exchange->profit,
                'currency_id' => $exchange->to_currency_id,
                'description' => 'Exchange profit',
            ];
        }

        $entry = $this->ledgerService->createJournalEntry([
            'date' => now()->toDateString(),
            'description' => "Exchange #{$exchange->code} - Currency exchange",
            'reference_type' => Exchange::class,
            'reference_id' => $exchange->id,
            'lines' => $lines,
        ]);

        $this->ledgerService->postJournalEntry($entry);

        $exchange->update(['journal_entry_id' => $entry->id]);
    }

    public function updateExchangeRate(int $fromCurrencyId, int $toCurrencyId, float $rate): ExchangeRate
    {
        return ExchangeRate::updateOrCreate(
            [
                'from_currency_id' => $fromCurrencyId,
                'to_currency_id' => $toCurrencyId,
            ],
            [
                'rate' => $rate,
                'source' => 'manual',
            ]
        );
    }

    public function getExchangeHistory(?int $customerId = null, ?string $startDate = null, ?string $endDate = null)
    {
        $query = Exchange::with(['fromCurrency', 'toCurrency', 'customer']);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getTotalProfit(?string $startDate = null, ?string $endDate = null): float
    {
        $query = Exchange::where('status', 'completed');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return (float) $query->sum('profit');
    }

    public function getCurrencyExposure(): array
    {
        $currencies = Currency::all();
        $exposure = [];

        foreach ($currencies as $currency) {
            $cashAccounts = Account::where('type', 'cash')
                ->where('currency_id', $currency->id)
                ->get();

            $totalBalance = 0;
            foreach ($cashAccounts as $account) {
                $balance = $this->ledgerService->computeDynamicBalance($account->id);
                $totalBalance = bcadd($totalBalance, $balance['balance'], 4);
            }

            $exposure[$currency->code] = [
                'currency_id' => $currency->id,
                'balance' => $totalBalance,
            ];
        }

        return $exposure;
    }
}