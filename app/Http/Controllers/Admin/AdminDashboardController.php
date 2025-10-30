<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\User;
use App\Models\Studio;
use App\Models\Food;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'totalMovies' => Movie::count(),
                'totalUsers' => User::count(),
                'totalStudios' => Studio::count(),
                'totalFoodItems' => Food::count(),
            ]
        ]);
    }
}