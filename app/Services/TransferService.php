<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\Account;
use App\Models\PartnerLedger;
use App\Models\CustomerLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferService
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function createTransfer(array $data): Transfer
    {
        return DB::transaction(function () use ($data) {
            $code = $this->generateTransferCode();
            $otp = $this->generateOTP();

            $transfer = Transfer::create([
                'code' => $code,
                'sender_customer_id' => $data['sender_customer_id'],
                'receiver_name' => $data['receiver_name'],
                'receiver_phone' => $data['receiver_phone'] ?? null,
                'partner_id' => $data['partner_id'],
                'amount' => $data['amount'],
                'currency_id' => $data['currency_id'],
                'fee' => $data['fee'] ?? 0,
                'status' => 'pending',
                'otp_code' => $otp,
                'otp_expires_at' => now()->addHours(24),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->createTransferJournalEntry($transfer);

            return $transfer;
        });
    }

    protected function generateTransferCode(): string
    {
        $prefix = 'TRF';
        $date = now()->format('ymd');
        $random = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        return "{$prefix}{$date}{$random}";
    }

    public function generateOTP(): string
    {
        return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    protected function createTransferJournalEntry(Transfer $transfer): void
    {
        $cashAccount = Account::where('type', 'cash')
            ->where('currency_id', $transfer->currency_id)
            ->first();

        if (!$cashAccount) {
            throw new \Exception('Cash account not found for currency: ' . $transfer->currency->code);
        }

        $this->ledgerService->createJournalEntry([
            'date' => now()->toDateString(),
            'description' => "Transfer #{$transfer->code} - Received from sender",
            'reference_type' => Transfer::class,
            'reference_id' => $transfer->id,
            'lines' => [
                [
                    'account_id' => $cashAccount->id,
                    'debit' => $transfer->amount + $transfer->fee,
                    'currency_id' => $transfer->currency_id,
                    'description' => 'Cash received',
                ],
                [
                    'account_id' => $cashAccount->id,
                    'credit' => 0,
                    'currency_id' => $transfer->currency_id,
                    'description' => 'Payable to partner',
                ],
            ],
        ]);
    }

    public function markAsSent(Transfer $transfer): Transfer
    {
        return DB::transaction(function () use ($transfer) {
            if ($transfer->status !== 'pending') {
                throw new \Exception('Transfer must be in pending status to mark as sent.');
            }

            $transfer->update(['status' => 'sent']);

            $partnerAccount = Account::where('type', 'partner')
                ->where('currency_id', $transfer->currency_id)
                ->first();

            if ($partnerAccount) {
                $this->ledgerService->createJournalEntry([
                    'date' => now()->toDateString(),
                    'description' => "Transfer #{$transfer->code} - Sent to partner",
                    'reference_type' => Transfer::class,
                    'reference_id' => $transfer->id,
                    'lines' => [
                        [
                            'account_id' => $partnerAccount->id,
                            'debit' => $transfer->amount,
                            'currency_id' => $transfer->currency_id,
                            'description' => 'Payable to partner',
                        ],
                        [
                            'account_id' => $partnerAccount->id,
                            'credit' => 0,
                            'currency_id' => $transfer->currency_id,
                            'description' => 'Cash sent',
                        ],
                    ],
                ]);
            }

            PartnerLedger::create([
                'partner_id' => $transfer->partner_id,
                'journal_entry_id' => null,
                'amount' => $transfer->amount,
                'currency_id' => $transfer->currency_id,
                'direction' => 'debit',
                'description' => "Transfer #{$transfer->code}",
            ]);

            return $transfer;
        });
    }

    public function markAsPaid(Transfer $transfer, string $otp): Transfer
    {
        if (!$this->verifyOTP($transfer, $otp)) {
            throw new \Exception('Invalid OTP code.');
        }

        if ($transfer->status !== 'sent') {
            throw new \Exception('Transfer must be in sent status to mark as paid.');
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
            return $transfer;
        });
    }

    public function cancelTransfer(Transfer $transfer): Transfer
    {
        return DB::transaction(function () use ($transfer) {
            if (in_array($transfer->status, ['paid', 'settled'])) {
                throw new \Exception('Cannot cancel a paid or settled transfer.');
            }

            $transfer->update(['status' => 'cancelled']);
            return $transfer;
        });
    }

    public function verifyOTP(Transfer $transfer, string $otp): bool
    {
        if ($transfer->otp_code !== $otp) {
            return false;
        }

        if ($transfer->otp_expires_at && now()->greaterThan($transfer->otp_expires_at)) {
            return false;
        }

        return true;
    }

    public function getTransferSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Transfer::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return [
            'total' => $query->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'sent' => $query->where('status', 'sent')->count(),
            'paid' => $query->where('status', 'paid')->count(),
            'settled' => $query->where('status', 'settled')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
        ];
    }
}