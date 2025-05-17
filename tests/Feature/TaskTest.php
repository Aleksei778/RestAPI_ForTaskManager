<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Task;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     */


    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken($this->user)->plainTextToken;
    }

    public function test_user_can_create_task(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                        ->postJson('/api/tasks', [
                            'title' => 'Полить помидоры',
                            'description' => 'Помочь маме полить помидоры'
                        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message','task']);
    
        $this->assertDatabaseHas('tasks', [
            'title'=> 'Полить помидоры',
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_can_update_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'TestTask',
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                        ->patchJson("/api/tasks/{$task->id}", [
                            'title' => 'NewTestTask'
                        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message','task']);
        
            $this->assertDatabaseHas('tasks', [
                'id' => $task->id,
                'title'=> 'NewTestTask',
                'user_id' => $this->user->id
            ]);
    }
    
    public function test_user_can_get_his_tasks(): void
    {
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $anotherUser = User::factory()->create();
        Task::factory()->count(4)->create([
            'user_id' => $anotherUser->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
                        ->getJson('/api/tasks/');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    
}