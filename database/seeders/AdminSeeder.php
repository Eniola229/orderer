<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        Admin::create([
            'first_name'    => 'Super',
            'last_name'     => 'Admin',
            'email'         => 'superadmin@example.com',
            'password'      => Hash::make('password123'),
            'role'          => Admin::ROLE_SUPER_ADMIN,
            'is_active'     => 1,
            'last_login_at' => now(),
        ]);

        // Finance Admin
        Admin::create([
            'first_name'    => 'Finance',
            'last_name'     => 'Admin',
            'email'         => 'finance@example.com',
            'password'      => Hash::make('password123'),
            'role'          => Admin::ROLE_FINANCE_ADMIN,
            'is_active'     => 1,
            'last_login_at' => null,
        ]);

        // Support Admin
        Admin::create([
            'first_name'    => 'Support',
            'last_name'     => 'Admin',
            'email'         => 'support@example.com',
            'password'      => Hash::make('password123'),
            'role'          => Admin::ROLE_SUPPORT_ADMIN,
            'is_active'     => 1,
            'last_login_at' => null,
        ]);

        // Content Moderator
        Admin::create([
            'first_name'    => 'Content',
            'last_name'     => 'Moderator',
            'email'         => 'moderator@example.com',
            'password'      => Hash::make('password123'),
            'role'          => Admin::ROLE_CONTENT_MODERATOR,
            'is_active'     => 1,
            'last_login_at' => null,
        ]);

        // HR Admin
        Admin::create([
            'first_name'    => 'HR',
            'last_name'     => 'Admin',
            'email'         => 'hr@example.com',
            'password'      => Hash::make('password123'),
            'role'          => Admin::ROLE_HR,
            'is_active'     => 1,
            'last_login_at' => null,
        ]);

        // Optional: Create an inactive admin for testing
        Admin::create([
            'first_name'    => 'Inactive',
            'last_name'     => 'Admin',
            'email'         => 'inactive@example.com',
            'password'      => Hash::make('password123'),
            'role'          => Admin::ROLE_SUPPORT_ADMIN,
            'is_active'     => 0,
            'last_login_at' => null,
        ]);
    }
}