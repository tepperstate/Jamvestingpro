<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DebugAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Super Admin (2FA-exempt)
        Admin::updateOrCreate(
            ['email' => 'superadmin@p2m.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@2024!'),
                'is_super_admin' => true,
                'is_2fa_exempt' => true,
                'status' => (int) 1,
            ]
        );

        // 2. Standard Admin
        Admin::updateOrCreate(
            ['email' => 'admin@p2m.test'],
            [
                'name' => 'Standard Admin',
                'password' => Hash::make('Admin@2024!'),
                'is_super_admin' => false,
                'is_2fa_exempt' => false,
                'status' => 1,
            ]
        );

        // 3. Pro User (2FA-exempt, pre-funded)
        $user = User::updateOrCreate(
            ['email' => 'tester@p2m.test'],
            [
                'first_name' => 'Pro',
                'last_name' => 'Tester',
                'password' => Hash::make('Tester@2024!'),
                'is_2fa_exempt' => true,
                'status' => 1, // Email verified
                'user_id' => 'TESTER123',
                'phone' => '1234567890',
                'country' => 'United States',
            ]
        );

        // Fund Pro User's USD balance
        Balance::updateOrCreate(
            ['user_id' => $user->id, 'symbol' => 'USD'],
            [
                'name' => 'Dollar',
                'amount' => 50000.00,
                'demo' => 10000.00,
                'bitcoin' => 0.00,
                'bonus' => 0.00,
                'bonus_balance' => 0.00,
                'referral' => 0.00,
                'image' => null,
            ]
        );
    }
}
