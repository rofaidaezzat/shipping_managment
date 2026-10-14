<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds for default role accounts.
     */
    public function run(): void
    {
        // ────────────────────────────────────────────────
        //  DEFAULT PASSWORD (plain text): password123
        // ────────────────────────────────────────────────
        $plainPassword = 'password123';
        $password      = Hash::make($plainPassword);

        $users = [
            [
                'name'           => 'Master Super Admin',
                'email'          => 'superadmin@shipping.com',
                'password'       => $password,
                'role'           => 'Super Admin',
                'is_super_admin' => true,
            ],
            [
                'name'           => 'Operations Admin',
                'email'          => 'admin@shipping.com',
                'password'       => $password,
                'role'           => 'Admin',
                'is_super_admin' => false,
            ],
            [
                'name'           => 'Sarah Sales',
                'email'          => 'sales@shipping.com',
                'password'       => $password,
                'role'           => 'Sales',
                'is_super_admin' => false,
            ],
            [
                'name'           => 'Alex Accountant',
                'email'          => 'accountant@shipping.com',
                'password'       => $password,
                'role'           => 'Accountant',
                'is_super_admin' => false,
            ],
            [
                'name'           => 'John Employee',
                'email'          => 'employee@shipping.com',
                'password'       => $password,
                'role'           => 'Employee',
                'is_super_admin' => false,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
