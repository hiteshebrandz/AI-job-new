<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'HR Demo',
                'email' => 'hr@gmail.com',
                'password' => Hash::make('hr@gmail.com'),
                'role' => User::ROLE_HR,
            ],
            [
                'name' => 'User Demo',
                'email' => 'user@gmail.com',
                'password' => Hash::make('user@gmail.com'),
                'role' => User::ROLE_USER,
            ],
            [
                'name' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin@gmail.com'),
                'role' => User::ROLE_ADMIN,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account
            );
        }
    }
}
