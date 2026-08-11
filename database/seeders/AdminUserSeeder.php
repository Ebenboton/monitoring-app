<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@monitoring.local'],
            [
                'name'      => 'Super Admin',
                'password'  => bcrypt('Azerty1234@'),
                'role'      => 'super_admin',
                'is_active' => true,
            ]
        );
    }
}