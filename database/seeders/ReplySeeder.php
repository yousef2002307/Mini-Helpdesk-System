<?php

namespace Database\Seeders;

use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class ReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tickets
        $tickets = Ticket::all();

        foreach ($tickets as $ticket) {
            // Create 2 replies for each ticket, authored by the ticket owner
            Reply::factory(2)->create([
                'ticket_id' => $ticket->id,
                'user_id'   => $ticket->user_id,
            ]);
        }
    }
}
