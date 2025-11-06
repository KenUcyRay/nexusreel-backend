<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Movie;
use App\Models\FoodOrder;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $totalRevenue = Booking::where('booking_status', 'confirmed')->sum('total_amount');
        $totalBookings = Booking::count();
        $popularMovies = Movie::withCount('showtimes')->orderBy('showtimes_count', 'desc')->limit(5)->get();
        
        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_bookings' => $totalBookings,
            'popular_movies' => $popularMovies,
        ]);
    }

    public function incomeReport()
    {
        $income = Booking::where('booking_status', 'confirmed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        
        return response()->json($income);
    }

    public function expenseReport()
    {
        // Mock expense data - implement based on your business logic
        return response()->json([
            'monthly_expenses' => 50000000,
            'operational_costs' => 30000000,
            'maintenance' => 20000000,
        ]);
    }
}