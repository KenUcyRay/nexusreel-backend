<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\User;
use App\Models\Studio;
use App\Models\Food;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'totalMovies' => Movie::count(),
                'totalUsers' => User::where('role', 'user')->count(),
                'totalStudios' => Studio::count(),
                'totalFoodItems' => Food::count(),
            ]
        ]);
    }
}