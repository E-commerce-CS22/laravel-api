<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 30 admins with their users
        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'first_name' => "مدير",
                'last_name' => $i . "#",
                'email' => "admin{$i}@example.com",
                'username' => "admin{$i}",
                'phone' => "966500{$i}" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
                'status' => 'active', // Admins are active by default
            ]);

            Admin::create([
                'user_id' => $user->id,
            ]);
        }

        // Create 30 customers with their users
        $saudiCities = [
            'الرياض', 'جدة', 'مكة المكرمة', 'المدينة المنورة', 'الدمام',
            'الخبر', 'الظهران', 'الأحساء', 'الطائف', 'بريدة',
            'تبوك', 'خميس مشيط', 'الجبيل', 'نجران', 'ينبع'
        ];

        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'first_name' => "عميل",
                'last_name' => $i . "#",
                'email' => "customer{$i}@example.com",
                'username' => "customer{$i}",
                'phone' => "966555{$i}" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
                'status' => 'inactive', // Customers start as inactive
            ]);

            Customer::create([
                'user_id' => $user->id,
                'address' => "شارع " . rand(1, 50) . "، حي " . rand(1, 20),
                'city' => $saudiCities[array_rand($saudiCities)],
                'postal_code' => rand(11000, 99999),
            ]);
        }
    }
}
