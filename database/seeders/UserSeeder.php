<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@coffee.test',
            'phone' => '0901234567',
            'role' => UserRole::Admin,
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Nhân viên',
            'email' => 'staff@coffee.test',
            'phone' => '0907654321',
            'role' => UserRole::Staff,
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'customer@coffee.test',
            'phone' => '0912345678',
            'role' => UserRole::Customer,
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
