<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin User',    'email' => 'admin@sarafi.local',   'role' => 'admin',   'password' => Hash::make('password')],
            ['name' => 'Manager User',  'email' => 'manager@sarafi.local', 'role' => 'manager', 'password' => Hash::make('password')],
            ['name' => 'Cashier User',  'email' => 'cashier@sarafi.local', 'role' => 'cashier', 'password' => Hash::make('password')],
            ['name' => 'Auditor User',  'email' => 'auditor@sarafi.local', 'role' => 'auditor', 'password' => Hash::make('password')],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user + ['is_active' => true]);
        }
    }
}
