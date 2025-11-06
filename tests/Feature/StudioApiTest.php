<?php

namespace Tests\Feature;

use App\Models\Studio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudioApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_admin_can_list_studios()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Studio::factory()->create(['name' => 'Test Studio']);
        
        Sanctum::actingAs($admin);
        
        $response = $this->getJson('/api/studios');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'type',
                            'status',
                            'rows',
                            'columns',
                            'total_seats'
                        ]
                    ]
                ]);
    }

    public function test_admin_can_create_studio()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        Sanctum::actingAs($admin);
        
        $studioData = [
            'name' => 'New Studio',
            'type' => 'Regular',
            'status' => 'active',
            'rows' => 10,
            'columns' => 14
        ];
        
        $response = $this->postJson('/api/studios', $studioData);
        
        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'name' => 'New Studio',
                        'total_seats' => 140
                    ]
                ]);
    }
}