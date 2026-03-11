<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Whitelist;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin
        $admin = User::create([
            'name' => 'Суперадмин',
            'phone' => '+77001234567',
            'role' => 'superadmin',
            'verification_status' => 'approved',
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addYear(),
            'city' => 'Алматы',
            'specialization' => 'Администратор',
        ]);

        // Add admin phone to whitelist
        Whitelist::create([
            'phone' => '+77001234567',
            'added_by' => $admin->id,
        ]);

        // Add some test phones to whitelist
        $testPhones = ['+77011111111', '+77022222222', '+77033333333', '+77044444444', '+77055555555'];
        foreach ($testPhones as $phone) {
            Whitelist::create([
                'phone' => $phone,
                'added_by' => $admin->id,
            ]);
        }
    }
}
