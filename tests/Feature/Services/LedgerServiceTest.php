<?php

namespace Tests\Feature\Services;

use App\Models\Account;
use App\Models\Currency;
use App\Models\JournalEntry;
use App\Models\User;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerServiceTest extends TestCase
{
    use RefreshDatabase;

    private LedgerService $ledger;
    private Currency $currency;
    private Account $cashAccount;
    private Account $revenueAccount;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ledger = app(LedgerService::class);

        $this->currency = Currency::create(['code' => 'AFN', 'name' => 'Afghani', 'symbol' => '؋', 'is_base' => true]);

        $this->cashAccount = Account::create([
            'code' => '1000', 'name' => 'Cash AFN', 'type' => 'cash',
            'currency_id' => $this->currency->id, 'is_active' => true,
        ]);

        $this->revenueAccount = Account::create([
            'code' => '4000', 'name' => 'Revenue', 'type' => 'revenue',
            'currency_id' => $this->currency->id, 'is_active' => true,
        ]);

        $this->user = User::create([
            'name' => 'Test', 'email' => 'test@test.com',
            'password' => bcrypt('password'), 'role' => 'admin', 'is_active' => true,
        ]);

        $this->actingAs($this->user);
    }

    public function test_creates_balanced_journal_entry(): void
    {
        $entry = $this->ledger->createJournalEntry([
            'description' => 'Test entry',
            'lines' => [
                ['account_id' => $this->cashAccount->id,    'debit' => 1000, 'credit' => 0,    'currency_id' => $this->currency->id],
                ['account_id' => $this->revenueAccount->id, 'debit' => 0,    'credit' => 1000, 'currency_id' => $this->currency->id],
            ],
        ]);

        $this->assertInstanceOf(JournalEntry::class, $entry);
        $this->assertEquals('draft', $entry->status);
        $this->assertTrue($entry->isBalanced());
        $this->assertCount(2, $entry->lines);
    }

    public function test_rejects_unbalanced_journal_entry(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/balanced/i');

        $this->ledger->createJournalEntry([
            'description' => 'Unbalanced',
            'lines' => [
                ['account_id' => $this->cashAccount->id, 'debit' => 1000, 'credit' => 0, 'currency_id' => $this->currency->id],
                ['account_id' => $this->revenueAccount->id, 'debit' => 0, 'credit' => 500, 'currency_id' => $this->currency->id],
            ],
        ]);
    }

    public function test_posts_draft_journal_entry(): void
    {
        $entry = $this->ledger->createJournalEntry([
            'description' => 'Post test',
            'lines' => [
                ['account_id' => $this->cashAccount->id,    'debit' => 500, 'credit' => 0,   'currency_id' => $this->currency->id],
                ['account_id' => $this->revenueAccount->id, 'debit' => 0,   'credit' => 500, 'currency_id' => $this->currency->id],
            ],
        ]);

        $posted = $this->ledger->postJournalEntry($entry);

        $this->assertEquals('approved', $posted->status);
        $this->assertNotNull($posted->approved_by_id);
    }

    public function test_cannot_post_already_approved_entry(): void
    {
        $this->expectException(\Exception::class);

        $entry = $this->ledger->createJournalEntry([
            'description' => 'Double post',
            'lines' => [
                ['account_id' => $this->cashAccount->id,    'debit' => 200, 'credit' => 0,   'currency_id' => $this->currency->id],
                ['account_id' => $this->revenueAccount->id, 'debit' => 0,   'credit' => 200, 'currency_id' => $this->currency->id],
            ],
        ]);

        $this->ledger->postJournalEntry($entry);
        $this->ledger->postJournalEntry($entry->fresh()); // should throw
    }

    public function test_reverses_approved_entry(): void
    {
        $entry = $this->ledger->createJournalEntry([
            'description' => 'Reversal test',
            'lines' => [
                ['account_id' => $this->cashAccount->id,    'debit' => 300, 'credit' => 0,   'currency_id' => $this->currency->id],
                ['account_id' => $this->revenueAccount->id, 'debit' => 0,   'credit' => 300, 'currency_id' => $this->currency->id],
            ],
        ]);

        $this->ledger->postJournalEntry($entry);
        $reversal = $this->ledger->reverseJournalEntry($entry->fresh());

        $this->assertEquals('reversed', $entry->fresh()->status);
        $this->assertEquals('approved', $reversal->status);
        $this->assertTrue($reversal->isBalanced());
    }

    public function test_computes_dynamic_balance(): void
    {
        $entry = $this->ledger->createJournalEntry([
            'description' => 'Balance test',
            'lines' => [
                ['account_id' => $this->cashAccount->id,    'debit' => 1000, 'credit' => 0,    'currency_id' => $this->currency->id],
                ['account_id' => $this->revenueAccount->id, 'debit' => 0,    'credit' => 1000, 'currency_id' => $this->currency->id],
            ],
        ]);
        $this->ledger->postJournalEntry($entry);

        $balance = $this->ledger->computeDynamicBalance($this->cashAccount->id);

        $this->assertEquals('1000.0000', $balance['balance']);
    }
}
