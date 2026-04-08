<?php

namespace App\Providers;

use App\Services\ExchangeService;
use App\Services\LedgerService;
use App\Services\SettlementService;
use App\Services\TransferService;
use App\Models\Exchange;
use App\Models\JournalEntry;
use App\Models\Settlement;
use App\Models\Transfer;
use App\Policies\ExchangePolicy;
use App\Policies\JournalEntryPolicy;
use App\Policies\SettlementPolicy;
use App\Policies\TransferPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Transfer::class     => TransferPolicy::class,
        JournalEntry::class => JournalEntryPolicy::class,
        Exchange::class     => ExchangePolicy::class,
        Settlement::class   => SettlementPolicy::class,
    ];

    public function register(): void
    {
        $this->app->singleton(LedgerService::class, function ($app) {
            return new LedgerService();
        });

        $this->app->singleton(TransferService::class, function ($app) {
            return new TransferService($app->make(LedgerService::class));
        });

        $this->app->singleton(ExchangeService::class, function ($app) {
            return new ExchangeService($app->make(LedgerService::class));
        });

        $this->app->singleton(SettlementService::class, function ($app) {
            return new SettlementService($app->make(LedgerService::class));
        });
    }

    public function boot(): void
    {
        $this->registerPolicies();
        Route::redirect('/login', '/admin/login')->name('login');
        Route::redirect('/register', '/admin/login')->name('register');
    }
}