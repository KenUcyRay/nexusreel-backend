<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Studio;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'studio_id' => Studio::factory(),
            'show_date' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'show_time' => $this->faker->time('H:i'),
            'price' => $this->faker->numberBetween(30000, 100000),
        ];
    }
}