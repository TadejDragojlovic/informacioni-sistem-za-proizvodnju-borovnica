<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin 1',
                'email' => 'admin@borovnica.com',
                'role' => UserRole::ADMIN->value,
                'password' => 'admin',
            ],
            [
                'name' => 'Zaposleni 1',
                'email' => 'zaposleni@borovnica.com',
                'role' => UserRole::ZAPOSLENI->value,
                'password' => 'zaposleni',
            ],
            [
                'name' => 'Kupac 1',
                'email' => 'kupac@borovnica.com',
                'role' => UserRole::KUPAC->value,
                'password' => 'kupac',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'password' => Hash::make($data['password']),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
