<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'company_name',       'value' => 'Digi Sarafi',          'description' => 'Company name shown on reports'],
            ['key' => 'company_phone',      'value' => '+93 799 000 000',       'description' => 'Company contact phone'],
            ['key' => 'base_currency',      'value' => 'AFN',                   'description' => 'Base/reporting currency code'],
            ['key' => 'otp_expiry_hours',   'value' => '24',                    'description' => 'OTP expiry in hours for transfers'],
            ['key' => 'transfer_fee_default','value' => '0',                    'description' => 'Default transfer fee amount'],
            ['key' => 'require_cash_session','value' => 'false',                'description' => 'Require open cash session for transactions'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
