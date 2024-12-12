<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminData = [
            [
                'first_name' => 'عبدالملك',
                'last_name' => 'الحميري',
                'email' => 'abdulmalik.homairi@example.com',
                'username' => 'abdulmalik_homairi',
                'phone' => '967734123456',
            ],
            [
                'first_name' => 'صالح',
                'last_name' => 'العولقي',
                'email' => 'saleh.awlaqi@example.com',
                'username' => 'saleh_awlaqi',
                'phone' => '967735234567',
            ],
            [
                'first_name' => 'عمار',
                'last_name' => 'الشرعبي',
                'email' => 'ammar.sharabi@example.com',
                'username' => 'ammar_sharabi',
                'phone' => '967736345678',
            ],
            [
                'first_name' => 'بلقيس',
                'last_name' => 'المقطري',
                'email' => 'balqees.maqtari@example.com',
                'username' => 'balqees_maqtari',
                'phone' => '967737456789',
            ],
            [
                'first_name' => 'همدان',
                'last_name' => 'الأغبري',
                'email' => 'hamdan.aghbari@example.com',
                'username' => 'hamdan_aghbari',
                'phone' => '967738567890',
            ],
        ];

        foreach ($adminData as $data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'phone' => $data['phone'],
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]);

            Admin::create([
                'user_id' => $user->id,
            ]);
        }
    }
}
