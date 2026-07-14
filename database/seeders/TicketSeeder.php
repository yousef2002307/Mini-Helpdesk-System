<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get standard users (non-admins)
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            // Create 2 tickets for each standard user
            Ticket::factory(2)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
