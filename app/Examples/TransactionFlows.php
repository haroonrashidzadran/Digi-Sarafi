<?php

namespace App\Examples;

use App\Models\Currency;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Partner;
use App\Services\LedgerService;
use App\Services\ExchangeService;
use App\Services\TransferService;
use App\Services\SettlementService;

class TransactionFlows
{
    protected LedgerService $ledgerService;
    protected ExchangeService $exchangeService;
    protected TransferService $transferService;
    protected SettlementService $settlementService;

    public function __construct(
        LedgerService $ledgerService,
        ExchangeService $exchangeService,
        TransferService $transferService,
        SettlementService $settlementService
    ) {
        $this->ledgerService = $ledgerService;
        $this->exchangeService = $exchangeService;
        $this->transferService = $transferService;
        $this->settlementService = $settlementService;
    }

    public function exampleCurrencyExchange(): void
    {
        $usd = Currency::where('code', 'USD')->first();
        $afn = Currency::where('code', 'AFN')->first();
        $customer = Customer::first();

        $exchange = $this->exchangeService->executeExchange([
            'customer_id' => $customer->id,
            'from_currency_id' => $usd->id,
            'to_currency_id' => $afn->id,
            'amount_from' => 100.00,
            'rate' => 68.50,
        ]);

        echo "Exchange completed: {$exchange->code}\n";
        echo "Customer gave: {$exchange->amount_from} USD\n";
        echo "Customer received: {$exchange->amount_to} AFN\n";
        echo "Profit: {$exchange->profit} AFN\n";
    }

    public function exampleHawalaTransfer(): void
    {
        $afn = Currency::where('code', 'AFN')->first();
        $customer = Customer::first();
        $partner = Partner::first();

        $transfer = $this->transferService->createTransfer([
            'sender_customer_id' => $customer->id,
            'receiver_name' => 'Rahmatullah',
            'receiver_phone' => '0798123456',
            'partner_id' => $partner->id,
            'amount' => 50000.00,
            'currency_id' => $afn->id,
            'fee' => 500.00,
        ]);

        echo "Transfer created: {$transfer->code}\n";
        echo "OTP: {$transfer->otp_code}\n";
        echo "Status: {$transfer->status}\n";

        $this->transferService->markAsSent($transfer);
        echo "Transfer marked as sent\n";

        $this->transferService->markAsPaid($transfer, $transfer->otp_code);
        echo "Transfer marked as paid\n";

        $this->transferService->settleTransfer($transfer);
        echo "Transfer settled\n";
    }

    public function exampleSettlementWithPartner(): void
    {
        $partner = Partner::first();
        $afn = Currency::where('code', 'AFN')->first();

        $settlement = $this->settlementService->createSettlement([
            'partner_id' => $partner->id,
            'amount' => 100000.00,
            'currency_id' => $afn->id,
            'type' => 'cash',
            'description' => 'Monthly settlement',
        ]);

        echo "Settlement completed: {$settlement->code}\n";

        $balance = $this->settlementService->getPartnerBalance($partner->id);
        echo "Partner balance after settlement: {$balance['balance']} AFN\n";
    }

    public function exampleManualJournalEntry(): void
    {
        $afn = Currency::where('code', 'AFN')->first();
        $cashAccount = Account::where('code', '1000')->first();
        $revenueAccount = Account::where('code', '4000')->first();

        $entry = $this->ledgerService->createJournalEntry([
            'date' => now()->toDateString(),
            'description' => 'Manual cash deposit',
            'lines' => [
                [
                    'account_id' => $cashAccount->id,
                    'debit' => 100000.00,
                    'credit' => 0.00,
                    'currency_id' => $afn->id,
                    'description' => 'Cash received',
                ],
                [
                    'account_id' => $revenueAccount->id,
                    'debit' => 0.00,
                    'credit' => 100000.00,
                    'currency_id' => $afn->id,
                    'description' => 'Capital introduced',
                ],
            ],
        ]);

        echo "Journal entry created: {$entry->id}\n";

        $this->ledgerService->postJournalEntry($entry);
        echo "Journal entry posted\n";
    }

    public function exampleGetReports(): void
    {
        $cashPosition = $this->ledgerService->computeCashPosition();
        echo "Cash Position:\n";
        foreach ($cashPosition['accounts'] as $code => $data) {
            echo "  {$code}: {$data['balance']} {$data['currency']}\n";
        }
        echo "Total: {$cashPosition['total']} {$cashPosition['currency']}\n\n";

        $profitLoss = $this->ledgerService->getProfitAndLoss(
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );
        echo "Profit & Loss (This Month):\n";
        echo "  Revenue: {$profitLoss['revenue']} AFN\n";
        echo "  Expenses: {$profitLoss['expenses']} AFN\n";
        echo "  Net Profit: {$profitLoss['profit']} AFN\n\n";

        $partnerBalances = $this->settlementService->getAllPartnerBalances();
        echo "Partner Balances:\n";
        foreach ($partnerBalances as $id => $data) {
            echo "  {$data['partner_name']}: {$data['balance']['balance']} AFN\n";
        }
    }
}