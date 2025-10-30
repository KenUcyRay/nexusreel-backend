<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\User;
use App\Models\Studio;
use App\Models\Food;
use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = [
            'totalMovies' => Movie::count(),
            'totalUsers' => User::where('role', 'user')->count(),
            'totalStudios' => Studio::count(),
            'totalFoodItems' => Food::count(),
            'totalBookings' => Booking::count(),
            'totalSchedules' => Schedule::count(),
            'activeMovies' => Movie::where('status', 'live_now')->count(),
            'comingSoonMovies' => Movie::where('status', 'coming_soon')->count(),
            'activeStudios' => Studio::where('status', 'active')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}