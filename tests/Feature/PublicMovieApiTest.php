<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Studio;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicMovieApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    public function test_can_get_public_movies()
    {
        Movie::factory()->create(['status' => 'live_now']);
        Movie::factory()->create(['status' => 'coming_soon']);
        
        $response = $this->getJson('/api/movies');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'genre',
                            'duration',
                            'rating',
                            'image'
                        ]
                    ]
                ])
                ->assertJsonCount(1, 'data'); // Only live_now movies
    }

    public function test_can_get_movie_with_schedules()
    {
        $movie = Movie::factory()->create(['status' => 'live_now']);
        $studio = Studio::factory()->create();
        
        Schedule::factory()->create([
            'movie_id' => $movie->id,
            'studio_id' => $studio->id,
            'show_date' => now()->addDay()->format('Y-m-d')
        ]);
        
        $response = $this->getJson("/api/movies/{$movie->id}");
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'name',
                        'schedules' => [
                            '*' => [
                                'id',
                                'show_date',
                                'show_time',
                                'price',
                                'studio'
                            ]
                        ]
                    ]
                ]);
    }

    public function test_can_get_coming_soon_movies()
    {
        Movie::factory()->create(['status' => 'live_now']);
        Movie::factory()->create(['status' => 'coming_soon']);
        
        $response = $this->getJson('/api/movies/coming-soon');
        
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data'); // Only coming_soon movies
    }
}