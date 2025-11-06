<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalMovies = Movie::count();
        $totalBookings = Booking::count();
        $totalRevenue = Booking::where('booking_status', 'confirmed')->sum('total_amount');
        
        $recentBookings = Booking::with('user', 'showtime.movie')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'stats' => [
                'total_users' => $totalUsers,
                'total_movies' => $totalMovies,
                'total_bookings' => $totalBookings,
                'total_revenue' => $totalRevenue,
            ],
            'recent_bookings' => $recentBookings,
        ]);
    }
}