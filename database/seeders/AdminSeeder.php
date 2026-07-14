<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Create the default admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@admin.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('12345678'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('Admin user created: test@admin.com / 12345678');
    }
}
