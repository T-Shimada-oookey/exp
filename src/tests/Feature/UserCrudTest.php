<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_update_and_delete_user(): void
    {
        // CREATE
        $user = User::factory()->create([
            'name' => 'tuna',
            'email' => 'tuna@example.com',
        ]);
        $this->assertDatabaseHas('users', ['email' => 'tuna@example.com']);

        // UPDATE
        $user->update(['name' => 'sakana']);
        $this->assertDatabaseHas('users', ['email' => 'tuna@example.com', 'name' => 'sakana']);

        // DELETE
        $user->delete();
        $this->assertDatabaseMissing('users', ['email' => 'tuna@example.com']);
    }
}