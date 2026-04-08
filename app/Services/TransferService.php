<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\Account;
use App\Models\PartnerLedger;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function createTransfer(array $data): Transfer
    {
        return DB::transaction(function () use ($data) {
            $transfer = Transfer::create([
                'code'               => $this->generateCode('TRF'),
                'sender_customer_id' => $data['sender_customer_id'],
                'receiver_name'      => $data['receiver_name'],
                'receiver_phone'     => $data['receiver_phone'] ?? null,
                'partner_id'         => $data['partner_id'],
                'amount'             => $data['amount'],
                'currency_id'        => $data['currency_id'],
                'fee'                => $data['fee'] ?? 0,
                'status'             => 'pending',
                'otp_code'           => $this->generateOTP(),
                'otp_expires_at'     => now()->addHours(24),
                'notes'              => $data['notes'] ?? null,
            ]);

            // DR Cash (customer pays amount + fee)
            // CR Partner Payable (we owe partner the amount)
            // CR Transfer Fee Income (fee earned)
            $cashAccount    = $this->requireAccount('cash', $transfer->currency_id);
            $partnerAccount = $this->requireAccount('partner', $transfer->currency_id);
            $feeAccount     = Account::where('type', 'revenue')
                ->where('name', 'like', '%Transfer Fee%')
                ->first() ?? $this->requireAccount('revenue', $transfer->currency_id);

            $lines = [
                ['account_id' => $cashAccount->id,    'debit'  => bcadd($transfer->amount, $transfer->fee, 4), 'credit' => 0, 'currency_id' => $transfer->currency_id, 'description' => 'Cash received from sender'],
                ['account_id' => $partnerAccount->id, 'debit'  => 0, 'credit' => $transfer->amount, 'currency_id' => $transfer->currency_id, 'description' => 'Payable to partner'],
            ];

            if (bccomp($transfer->fee, 0, 4) > 0) {
                $lines[] = ['account_id' => $feeAccount->id, 'debit' => 0, 'credit' => $transfer->fee, 'currency_id' => $transfer->currency_id, 'description' => 'Transfer fee income'];
            }

            $entry = $this->ledgerService->createJournalEntry([
                'description'    => "Transfer #{$transfer->code} - Cash received from sender",
                'reference_type' => Transfer::class,
                'reference_id'   => $transfer->id,
                'lines'          => $lines,
            ]);

            $this->ledgerService->postJournalEntry($entry);
            $transfer->update(['journal_entry_id' => $entry->id]);

            return $transfer;
        });
    }

    public function markAsSent(Transfer $transfer): Transfer
    {
        return DB::transaction(function () use ($transfer) {
            if ($transfer->status !== 'pending') {
                throw new \Exception('Transfer must be pending to mark as sent.');
            }

            $transfer->update(['status' => 'sent']);

            PartnerLedger::create([
                'partner_id'      => $transfer->partner_id,
                'journal_entry_id'=> $transfer->journal_entry_id,
                'amount'          => $transfer->amount,
                'currency_id'     => $transfer->currency_id,
                'direction'       => 'debit',
                'description'     => "Transfer #{$transfer->code} sent",
            ]);

            return $transfer;
        });
    }

    public function markAsPaid(Transfer $transfer, string $otp): Transfer
    {
        if (!$this->verifyOTP($transfer, $otp)) {
            throw new \Exception('Invalid or expired OTP code.');
        }

        if ($transfer->status !== 'sent') {
            throw new \Exception('Transfer must be sent to mark as paid.');
        }

        $transfer->update(['status' => 'paid']);
        return $transfer;
    }

    public function settleTransfer(Transfer $transfer): Transfer
    {
        return DB::transaction(function () use ($transfer) {
            if ($transfer->status !== 'paid') {
                throw new \Exception('Transfer must be paid before settlement.');
            }

            $transfer->update(['status' => 'settled']);

            PartnerLedger::create([
                'partner_id'       => $transfer->partner_id,
                'journal_entry_id' => $transfer->journal_entry_id,
                'amount'           => $transfer->amount,
                'currency_id'      => $transfer->currency_id,
                'direction'        => 'credit',
                'description'      => "Transfer #{$transfer->code} settled",
            ]);

            return $transfer;
        });
    }

    public function cancelTransfer(Transfer $transfer): Transfer
    {
        return DB::transaction(function () use ($transfer) {
            if (in_array($transfer->status, ['paid', 'settled'])) {
                throw new \Exception('Cannot cancel a paid or settled transfer.');
            }

            if ($transfer->journal_entry_id) {
                $entry = $transfer->journalEntry;
                if ($entry && $entry->status === 'approved') {
                    $this->ledgerService->reverseJournalEntry($entry);
                }
            }

            $transfer->update(['status' => 'cancelled']);
            return $transfer;
        });
    }

    public function verifyOTP(Transfer $transfer, string $otp): bool
    {
        return $transfer->otp_code === $otp
            && (!$transfer->otp_expires_at || now()->lessThanOrEqualTo($transfer->otp_expires_at));
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

    public function generateOTP(): string
    {
        return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
