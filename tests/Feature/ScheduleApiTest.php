<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\Movie;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScheduleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_admin_can_create_schedule()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $movie = Movie::factory()->create();
        $studio = Studio::factory()->create();
        
        Sanctum::actingAs($admin);
        
        $scheduleData = [
            'movie_id' => $movie->id,
            'studio_id' => $studio->id,
            'show_date' => now()->addDay()->format('Y-m-d'),
            'show_time' => '14:30',
            'price' => 50000
        ];
        
        $response = $this->postJson('/api/admin/schedules', $scheduleData);
        
        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'movie_id' => $movie->id,
                        'studio_id' => $studio->id,
                        'price' => '50000.00'
                    ]
                ]);
    }

    public function test_cannot_double_book_studio()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $movie = Movie::factory()->create();
        $studio = Studio::factory()->create();
        
        Schedule::factory()->create([
            'studio_id' => $studio->id,
            'show_date' => now()->addDay()->format('Y-m-d'),
            'show_time' => '14:30'
        ]);
        
        Sanctum::actingAs($admin);
        
        $scheduleData = [
            'movie_id' => $movie->id,
            'studio_id' => $studio->id,
            'show_date' => now()->addDay()->format('Y-m-d'),
            'show_time' => '14:30',
            'price' => 50000
        ];
        
        $response = $this->postJson('/api/admin/schedules', $scheduleData);
        
        $response->assertStatus(422);
    }
}