<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NormalUser extends Seeder
{
    /**
     * Create the default admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@user.com'],
            [
                'name'     => 'Normal User',
                'password' => Hash::make('12345678'),
                'role'     => 'user',
            ]
        );

        $this->command->info('Normal user created: test@user.com / 12345678');
    }
}
