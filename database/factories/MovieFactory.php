<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'image' => 'movies/default.jpg',
            'duration' => $this->faker->numberBetween(90, 180),
            'genre' => $this->faker->randomElement(['Action', 'Comedy', 'Drama', 'Horror', 'Romance', 'Sci-Fi']),
            'rating' => $this->faker->randomElement(['G', 'PG', 'PG-13', 'R', 'NC-17']),
            'director' => $this->faker->name(),
            'production_team' => $this->faker->company(),
            'trailer_type' => 'url',
            'trailer_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => $this->faker->randomElement(['live_now', 'coming_soon']),
        ];
    }
}