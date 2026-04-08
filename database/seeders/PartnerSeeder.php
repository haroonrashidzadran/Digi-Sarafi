<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            ['code' => 'P001', 'name' => 'Kabul Exchange House', 'city' => 'Kabul', 'country' => 'Afghanistan', 'phone' => '0799123456', 'trust_level' => 'trusted'],
            ['code' => 'P002', 'name' => 'Peshawar Sarafi', 'city' => 'Peshawar', 'country' => 'Pakistan', 'phone' => '0092312123456', 'trust_level' => 'high'],
            ['code' => 'P003', 'name' => 'Tehran Exchange', 'city' => 'Tehran', 'country' => 'Iran', 'phone' => '00982112345678', 'trust_level' => 'medium'],
            ['code' => 'P004', 'name' => 'Dubai Sarafi', 'city' => 'Dubai', 'country' => 'UAE', 'phone' => '0097141234567', 'trust_level' => 'trusted'],
            ['code' => 'P005', 'name' => 'Quetta Sarafi', 'city' => 'Quetta', 'country' => 'Pakistan', 'phone' => '008165123456', 'trust_level' => 'high'],
        ];

        foreach ($partners as $partner) {
            Partner::create($partner);
        }
    }
}