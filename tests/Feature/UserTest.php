<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_admin_can_get_all_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        User::factory()->count(5)->create();

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonCount(6, 'data');
    }

    public function test_admin_cant_get_all_users(): void
    {
        $admin = User::factory()->create(['role' => 'user']);

        User::factory()->count(5)->create();

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_user() 
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $userToDelete = User::factory()->create();

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->deleteJson('/api/user/' . $userToDelete->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
        ]);
    }
}
