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
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@example.com',
                'password' => Hash::make('password'),
                'role' => 'hr',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Staff Member',
                'email' => 'staff@example.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('✅ Users seeded successfully!');
    }
}
