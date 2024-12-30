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
                'name' => 'عبدالملك الحميري',
                'email' => 'abdulmalik.homairi@example.com',
                'username' => 'abdulmalik_homairi',
                'phone' => '967734123456',
            ],
            [
                'name' => 'صالح العولقي',
                'email' => 'saleh.awlaqi@example.com',
                'username' => 'saleh_awlaqi',
                'phone' => '967735234567',
            ],
            [
                'name' => 'عمار الشرعبي',
                'email' => 'ammar.sharabi@example.com',
                'username' => 'ammar_sharabi',
                'phone' => '967736345678',
            ],
            [
                'name' => 'بلقيس المقطري',
                'email' => 'balqees.maqtari@example.com',
                'username' => 'balqees_maqtari',
                'phone' => '967737456789',
            ],
            [
                'name' => 'همدان الأغبري',
                'email' => 'hamdan.aghbari@example.com',
                'username' => 'hamdan_aghbari',
                'phone' => '967738567890',
            ],
            [
                'name' => 'محمد المجيدي',
                'email' => 'hamdan.aghbari@example.com',
                'username' => 'hamdan_aghbari',
                'phone' => '967738567890',
            ],
        ];


        foreach ($adminData as $data) {
            $user = User::create([
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]);

            Admin::create([
                'user_id' => $user->id,
                'name' => $data['name'],
            ]);
        }
    }
}
