<?php

namespace Tests\Feature\Services;

use App\Models\Account;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransferService $service;
    private Currency $currency;
    private Customer $customer;
    private Partner $partner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(TransferService::class);

        $this->currency = Currency::create(['code' => 'AFN', 'name' => 'Afghani', 'symbol' => '؋', 'is_base' => true]);

        // Required accounts for journal entries
        Account::create(['code' => '1000', 'name' => 'Cash AFN',         'type' => 'cash',    'currency_id' => $this->currency->id, 'is_active' => true]);
        Account::create(['code' => '1300', 'name' => 'Partner Payable',  'type' => 'partner', 'currency_id' => $this->currency->id, 'is_active' => true]);
        Account::create(['code' => '4001', 'name' => 'Transfer Fee Income', 'type' => 'revenue', 'currency_id' => $this->currency->id, 'is_active' => true]);

        $this->customer = Customer::create(['code' => 'C001', 'name' => 'Ahmad', 'status' => 'active', 'preferred_currency_id' => $this->currency->id]);
        $this->partner  = Partner::create(['code' => 'P001', 'name' => 'Kabul Partner', 'trust_level' => 'high']);

        $user = User::create(['name' => 'Admin', 'email' => 'a@a.com', 'password' => bcrypt('p'), 'role' => 'admin', 'is_active' => true]);
        $this->actingAs($user);
    }

    public function test_creates_transfer_with_journal_entry(): void
    {
        $transfer = $this->service->createTransfer([
            'sender_customer_id' => $this->customer->id,
            'receiver_name'      => 'Rahmatullah',
            'partner_id'         => $this->partner->id,
            'amount'             => 50000,
            'currency_id'        => $this->currency->id,
            'fee'                => 500,
        ]);

        $this->assertEquals('pending', $transfer->status);
        $this->assertNotNull($transfer->otp_code);
        $this->assertNotNull($transfer->journal_entry_id);
        $this->assertEquals('approved', $transfer->journalEntry->status);
    }

    public function test_full_transfer_lifecycle(): void
    {
        $transfer = $this->service->createTransfer([
            'sender_customer_id' => $this->customer->id,
            'receiver_name'      => 'Rahmatullah',
            'partner_id'         => $this->partner->id,
            'amount'             => 10000,
            'currency_id'        => $this->currency->id,
        ]);

        $this->service->markAsSent($transfer);
        $this->assertEquals('sent', $transfer->fresh()->status);

        $this->service->markAsPaid($transfer->fresh(), $transfer->otp_code);
        $this->assertEquals('paid', $transfer->fresh()->status);

        $this->service->settleTransfer($transfer->fresh());
        $this->assertEquals('settled', $transfer->fresh()->status);
    }

    public function test_rejects_invalid_otp(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/OTP/i');

        $transfer = $this->service->createTransfer([
            'sender_customer_id' => $this->customer->id,
            'receiver_name'      => 'Test',
            'partner_id'         => $this->partner->id,
            'amount'             => 5000,
            'currency_id'        => $this->currency->id,
        ]);

        $this->service->markAsSent($transfer);
        $this->service->markAsPaid($transfer->fresh(), '0000'); // wrong OTP
    }

    public function test_cannot_cancel_settled_transfer(): void
    {
        $this->expectException(\Exception::class);

        $transfer = $this->service->createTransfer([
            'sender_customer_id' => $this->customer->id,
            'receiver_name'      => 'Test',
            'partner_id'         => $this->partner->id,
            'amount'             => 5000,
            'currency_id'        => $this->currency->id,
        ]);

        $this->service->markAsSent($transfer);
        $this->service->markAsPaid($transfer->fresh(), $transfer->otp_code);
        $this->service->settleTransfer($transfer->fresh());
        $this->service->cancelTransfer($transfer->fresh()); // should throw
    }
}
