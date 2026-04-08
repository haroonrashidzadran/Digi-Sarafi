<?php

namespace Tests\Feature\Services;

use App\Models\Account;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\ExchangeRate;
use App\Models\User;
use App\Services\ExchangeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExchangeService $service;
    private Currency $usd;
    private Currency $afn;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ExchangeService::class);

        $this->afn = Currency::create(['code' => 'AFN', 'name' => 'Afghani',   'symbol' => '؋', 'is_base' => true]);
        $this->usd = Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'is_base' => false]);

        Account::create(['code' => '1000', 'name' => 'Cash AFN', 'type' => 'cash',    'currency_id' => $this->afn->id, 'is_active' => true]);
        Account::create(['code' => '1001', 'name' => 'Cash USD', 'type' => 'cash',    'currency_id' => $this->usd->id, 'is_active' => true]);
        Account::create(['code' => '4000', 'name' => 'Exchange Profit', 'type' => 'revenue', 'currency_id' => $this->afn->id, 'is_active' => true]);

        ExchangeRate::create(['from_currency_id' => $this->usd->id, 'to_currency_id' => $this->afn->id, 'rate' => 70.00, 'source' => 'manual']);

        $user = User::create(['name' => 'Admin', 'email' => 'a@a.com', 'password' => bcrypt('p'), 'role' => 'admin', 'is_active' => true]);
        $this->actingAs($user);
    }

    public function test_executes_exchange_and_creates_journal_entry(): void
    {
        // Need a liability account for FX bridge
        Account::create(['code' => '2000', 'name' => 'FX Payable', 'type' => 'liability', 'currency_id' => $this->usd->id, 'is_active' => true]);

        $exchange = $this->service->executeExchange([
            'from_currency_id' => $this->usd->id,
            'to_currency_id'   => $this->afn->id,
            'amount_from'      => 100,
        ]);

        $this->assertEquals('completed', $exchange->status);
        $this->assertEquals(7000, $exchange->amount_to);
        $this->assertNotNull($exchange->journal_entry_id);
        $this->assertEquals('approved', $exchange->journalEntry->status);
    }

    public function test_uses_manual_rate_override(): void
    {
        Account::create(['code' => '2000', 'name' => 'FX Payable', 'type' => 'liability', 'currency_id' => $this->usd->id, 'is_active' => true]);

        $exchange = $this->service->executeExchange([
            'from_currency_id' => $this->usd->id,
            'to_currency_id'   => $this->afn->id,
            'amount_from'      => 100,
            'rate'             => 72.00,
        ]);

        $this->assertEquals(72.00, $exchange->rate);
        $this->assertEquals(7200, $exchange->amount_to);
    }

    public function test_throws_when_no_rate_available(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/rate/i');

        $pkr = Currency::create(['code' => 'PKR', 'name' => 'Rupee', 'symbol' => '₨', 'is_base' => false]);
        Account::create(['code' => '1002', 'name' => 'Cash PKR', 'type' => 'cash', 'currency_id' => $pkr->id, 'is_active' => true]);

        $this->service->executeExchange([
            'from_currency_id' => $pkr->id,
            'to_currency_id'   => $this->afn->id,
            'amount_from'      => 1000,
        ]);
    }

    public function test_journal_entry_is_balanced(): void
    {
        Account::create(['code' => '2000', 'name' => 'FX Payable', 'type' => 'liability', 'currency_id' => $this->usd->id, 'is_active' => true]);

        $exchange = $this->service->executeExchange([
            'from_currency_id' => $this->usd->id,
            'to_currency_id'   => $this->afn->id,
            'amount_from'      => 50,
        ]);

        $this->assertTrue($exchange->journalEntry->isBalanced());
    }
}
