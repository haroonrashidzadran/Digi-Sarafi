<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $afn = Currency::where('code', 'AFN')->first();
        $usd = Currency::where('code', 'USD')->first();

        $customers = [
            ['code' => 'C001', 'name' => 'Ahmadullah', 'email' => 'ahmad@example.com', 'phone' => '0799123456', 'preferred_currency_id' => $afn->id, 'status' => 'active'],
            ['code' => 'C002', 'name' => 'Mohammad Nazir', 'email' => 'nazir@example.com', 'phone' => '0789123456', 'preferred_currency_id' => $usd->id, 'status' => 'active'],
            ['code' => 'C003', 'name' => 'Farid Ahmad', 'email' => 'farid@example.com', 'phone' => '0779123456', 'preferred_currency_id' => $afn->id, 'status' => 'active'],
            ['code' => 'C004', 'name' => 'Sayed Kamal', 'email' => 'kamal@example.com', 'phone' => '0769123456', 'preferred_currency_id' => $usd->id, 'status' => 'active'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}