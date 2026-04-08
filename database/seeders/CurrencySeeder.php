<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'AFN', 'name' => 'Afghani', 'symbol' => '؋', 'is_base' => true],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'is_base' => false],
            ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => '₨', 'is_base' => false],
            ['code' => 'IRR', 'name' => 'Iranian Rial', 'symbol' => '﷼', 'is_base' => false],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'is_base' => false],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}