<?php

namespace App\Services;

use App\Models\Settlement;
use App\Models\PartnerLedger;
use App\Models\Account;
use App\Models\Partner;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function createSettlement(array $data): Settlement
    {
        return DB::transaction(function () use ($data) {
            $settlement = Settlement::create([
                'code'        => $this->generateCode('STL'),
                'partner_id'  => $data['partner_id'],
                'amount'      => $data['amount'],
                'currency_id' => $data['currency_id'],
                'type'        => $data['type'] ?? 'cash',
                'status'      => 'completed',
                'description' => $data['description'] ?? null,
            ]);

            $entry = $this->createSettlementJournalEntry($settlement);
            $settlement->update(['journal_entry_id' => $entry->id]);

            PartnerLedger::create([
                'partner_id'       => $settlement->partner_id,
                'journal_entry_id' => $entry->id,
                'amount'           => $settlement->amount,
                'currency_id'      => $settlement->currency_id,
                'direction'        => 'credit',
                'description'      => "Settlement #{$settlement->code}",
            ]);

            return $settlement;
        });
    }

    protected function createSettlementJournalEntry(Settlement $settlement)
    {
        $partnerAccount = $this->requireAccount('partner', $settlement->currency_id);

        if ($settlement->type === 'adjustment') {
            // Adjustment: DR Partner Payable / CR Partner Payable (net zero - use equity or suspense)
            $equityAccount = Account::where('type', 'equity')->where('currency_id', $settlement->currency_id)->first()
                ?? Account::where('type', 'equity')->first();

            if (!$equityAccount) {
                throw new \Exception('Equity account not found for adjustment settlement.');
            }

            $lines = [
                ['account_id' => $partnerAccount->id, 'debit' => $settlement->amount, 'credit' => 0, 'currency_id' => $settlement->currency_id, 'description' => 'Partner balance adjusted'],
                ['account_id' => $equityAccount->id,  'debit' => 0, 'credit' => $settlement->amount, 'currency_id' => $settlement->currency_id, 'description' => 'Adjustment offset'],
            ];
        } else {
            // Cash/Bank: DR Partner Payable / CR Cash
            $cashAccount = $this->requireAccount($settlement->type === 'bank' ? 'bank' : 'cash', $settlement->currency_id);

            $lines = [
                ['account_id' => $partnerAccount->id, 'debit' => $settlement->amount, 'credit' => 0, 'currency_id' => $settlement->currency_id, 'description' => 'Partner balance reduced'],
                ['account_id' => $cashAccount->id,    'debit' => 0, 'credit' => $settlement->amount, 'currency_id' => $settlement->currency_id, 'description' => 'Cash paid to partner'],
            ];
        }

        $entry = $this->ledgerService->createJournalEntry([
            'description'    => "Settlement #{$settlement->code} - {$settlement->type}",
            'reference_type' => Settlement::class,
            'reference_id'   => $settlement->id,
            'lines'          => $lines,
        ]);

        $this->ledgerService->postJournalEntry($entry);
        return $entry;
    }

    protected function requireAccount(string $type, int $currencyId): Account
    {
        $account = Account::where('type', $type)->where('currency_id', $currencyId)->first();
        if (!$account) {
            throw new \Exception("No {$type} account found for currency ID {$currencyId}.");
        }
        return $account;
    }

    protected function generateCode(string $prefix): string
    {
        return $prefix . now()->format('ymd') . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function getPartnerBalance(int $partnerId, ?int $currencyId = null): array
    {
        return $this->ledgerService->computePartnerExposure($partnerId, $currencyId);
    }

    public function getAllPartnerBalances(): array
    {
        return Partner::all()->mapWithKeys(fn ($p) => [
            $p->id => [
                'partner_code' => $p->code,
                'partner_name' => $p->name,
                'city'         => $p->city,
                'trust_level'  => $p->trust_level,
                'balance'      => $this->getPartnerBalance($p->id),
            ],
        ])->toArray();
    }
}
