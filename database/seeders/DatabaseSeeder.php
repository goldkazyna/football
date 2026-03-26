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
            'iin' => '000000000001',
            'role' => 'superadmin',
            'verification_status' => 'approved',
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addYear(),
            'city' => 'Алматы',
            'specialization' => 'Администратор',
        ]);

        // Add admin iin to whitelist
        Whitelist::create([
            'iin' => '000000000001',
            'added_by' => $admin->id,
        ]);

        // Add some test IINs to whitelist
        $testIins = ['111111111111', '222222222222', '333333333333', '444444444444', '555555555555'];
        foreach ($testIins as $iin) {
            Whitelist::create([
                'iin' => $iin,
                'added_by' => $admin->id,
            ]);
        }
    }
}
