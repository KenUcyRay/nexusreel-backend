<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Movie;
use App\Models\Studio;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $movies = Movie::all();
        $studios = Studio::all();

        if ($movies->isEmpty() || $studios->isEmpty()) {
            $this->command->info('Please seed movies and studios first');
            return;
        }

        $schedules = [
            [
                'movie_id' => $movies->first()->id,
                'studio_id' => $studios->first()->id,
                'show_date' => Carbon::today()->addDays(1),
                'show_time' => '14:00:00',
                'price' => 50000
            ],
            [
                'movie_id' => $movies->first()->id,
                'studio_id' => $studios->first()->id,
                'show_date' => Carbon::today()->addDays(1),
                'show_time' => '17:00:00',
                'price' => 60000
            ],
            [
                'movie_id' => $movies->first()->id,
                'studio_id' => $studios->skip(1)->first()->id ?? $studios->first()->id,
                'show_date' => Carbon::today()->addDays(2),
                'show_time' => '19:00:00',
                'price' => 70000
            ]
        ];

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }

        $this->command->info('Schedules seeded successfully');
    }
}