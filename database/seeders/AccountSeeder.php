<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $afn = Currency::where('code', 'AFN')->first();
        $usd = Currency::where('code', 'USD')->first();
        $pkr = Currency::where('code', 'PKR')->first();
        $irr = Currency::where('code', 'IRR')->first();
        $aed = Currency::where('code', 'AED')->first();

        $accounts = [
            ['code' => '1000', 'name' => 'Cash - AFN', 'type' => 'cash', 'currency_id' => $afn->id],
            ['code' => '1001', 'name' => 'Cash - USD', 'type' => 'cash', 'currency_id' => $usd->id],
            ['code' => '1002', 'name' => 'Cash - PKR', 'type' => 'cash', 'currency_id' => $pkr->id],
            ['code' => '1003', 'name' => 'Cash - IRR', 'type' => 'cash', 'currency_id' => $irr->id],
            ['code' => '1004', 'name' => 'Cash - AED', 'type' => 'cash', 'currency_id' => $aed->id],
            
            ['code' => '1100', 'name' => 'Bank - AFN', 'type' => 'bank', 'currency_id' => $afn->id],
            ['code' => '1101', 'name' => 'Bank - USD', 'type' => 'bank', 'currency_id' => $usd->id],
            
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'customer', 'currency_id' => $afn->id],
            ['code' => '1300', 'name' => 'Partner Payable - AFN', 'type' => 'partner', 'currency_id' => $afn->id],
            ['code' => '1301', 'name' => 'Partner Payable - USD', 'type' => 'partner', 'currency_id' => $usd->id],
            ['code' => '1302', 'name' => 'Partner Payable - PKR', 'type' => 'partner', 'currency_id' => $pkr->id],
            ['code' => '1303', 'name' => 'Partner Payable - AED', 'type' => 'partner', 'currency_id' => $aed->id],
            
            ['code' => '2000', 'name' => 'FX Payable - AFN',  'type' => 'liability', 'currency_id' => $afn->id],
            ['code' => '2001', 'name' => 'FX Payable - USD',  'type' => 'liability', 'currency_id' => $usd->id],
            ['code' => '2002', 'name' => 'FX Payable - PKR',  'type' => 'liability', 'currency_id' => $pkr->id],
            ['code' => '2003', 'name' => 'FX Payable - IRR',  'type' => 'liability', 'currency_id' => $irr->id],
            ['code' => '2004', 'name' => 'FX Payable - AED',  'type' => 'liability', 'currency_id' => $aed->id],
            
            ['code' => '3000', 'name' => 'Capital', 'type' => 'equity', 'currency_id' => $afn->id],
            
            ['code' => '4000', 'name' => 'Exchange Revenue', 'type' => 'revenue', 'currency_id' => $afn->id],
            ['code' => '4001', 'name' => 'Transfer Fee Income', 'type' => 'revenue', 'currency_id' => $afn->id],
            
            ['code' => '5000', 'name' => 'Operating Expenses', 'type' => 'expense', 'currency_id' => $afn->id],
            ['code' => '5001', 'name' => 'Bank Charges', 'type' => 'expense', 'currency_id' => $afn->id],
            ['code' => '5002', 'name' => 'Cost of Sales', 'type' => 'cost_of_sales', 'currency_id' => $afn->id],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}