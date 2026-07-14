<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an authenticated user can create a ticket successfully.
     */
    public function test_authenticated_user_can_create_a_ticket(): void
    {
       
        $user = User::factory()->create();

    
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/tickets', [
                'title'       => 'My First Support Ticket',
                'description' => 'I am experiencing issues with the service registration page.',
            ]);

     
        $response->assertStatus(201)
            ->assertJsonFragment([
                'status'  => 201,
                'success' => true,
                'message' => 'Ticket created successfully.',
            ])
            ->assertJsonStructure([
                'status',
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('tickets', [
            'user_id'     => $user->id,
            'title'       => 'My First Support Ticket',
            'description' => 'I am experiencing issues with the service registration page.',
            'status'      => 'open',
        ]);
    }

    /**
     * Test that an unauthenticated request to create a ticket is rejected.
     */
    public function test_unauthenticated_request_is_rejected(): void
    {
      
        $response = $this->postJson('/api/user/tickets', [
            'title'       => 'Unauthorized Ticket',
            'description' => 'Should fail anyway.',
        ]);

      
        $response->assertStatus(401);
    }

    /**
     * Test that ticket creation fails when validation constraints are not met.
     */
    public function test_ticket_creation_fails_with_missing_fields(): void
    {
      
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/tickets', []);

       
        $response->assertStatus(422)
            ->assertJsonFragment([
                'status'  => 422,
                'success' => false,
            ])
            ->assertJsonStructure([
                'status',
                'success',
                'message',
            ]);
    }
}
