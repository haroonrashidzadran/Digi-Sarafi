<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $usd = Currency::where('code', 'USD')->first();
        $pkr = Currency::where('code', 'PKR')->first();
        $irr = Currency::where('code', 'IRR')->first();
        $aed = Currency::where('code', 'AED')->first();
        $afn = Currency::where('code', 'AFN')->first();

        $rates = [
            ['from_currency_id' => $usd->id, 'to_currency_id' => $afn->id, 'rate' => 68.50, 'source' => 'manual'],
            ['from_currency_id' => $afn->id, 'to_currency_id' => $usd->id, 'rate' => 0.0146, 'source' => 'manual'],
            ['from_currency_id' => $pkr->id, 'to_currency_id' => $afn->id, 'rate' => 0.24, 'source' => 'manual'],
            ['from_currency_id' => $afn->id, 'to_currency_id' => $pkr->id, 'rate' => 4.15, 'source' => 'manual'],
            ['from_currency_id' => $irr->id, 'to_currency_id' => $afn->id, 'rate' => 0.0016, 'source' => 'manual'],
            ['from_currency_id' => $aed->id, 'to_currency_id' => $afn->id, 'rate' => 18.65, 'source' => 'manual'],
            ['from_currency_id' => $afn->id, 'to_currency_id' => $aed->id, 'rate' => 0.0536, 'source' => 'manual'],
            ['from_currency_id' => $usd->id, 'to_currency_id' => $pkr->id, 'rate' => 278.50, 'source' => 'manual'],
        ];

        foreach ($rates as $rate) {
            ExchangeRate::create($rate);
        }
    }
}