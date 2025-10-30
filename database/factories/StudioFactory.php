<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudioFactory extends Factory
{
    public function definition(): array
    {
        $rows = $this->faker->numberBetween(6, 15);
        $columns = $this->faker->numberBetween(10, 18);
        
        return [
            'name' => 'Studio ' . $this->faker->unique()->randomLetter(),
            'type' => $this->faker->randomElement(['Regular', 'Premium', 'IMAX', '4DX']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'rows' => $rows,
            'columns' => $columns,
            'total_seats' => $rows * $columns,
        ];
    }
}