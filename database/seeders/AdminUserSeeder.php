<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@demo.sch.id'],
            [
                'name' => 'Admin Sekolah',
                'full_name' => 'Administrator',
                'password' => bcrypt('Demo123!'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}

