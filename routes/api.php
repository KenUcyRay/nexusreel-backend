<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PublicMovieController;
use App\Http\Controllers\PublicFoodController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\FoodController as AdminFoodController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Test routes untuk debugging
Route::get('/test', function () {
    return response()->json([
        'message' => 'Backend connected successfully!', 
        'time' => now(),
        'server' => 'Laravel ' . app()->version()
    ]);
});

Route::get('/routes-check', function () {
    return response()->json([
        'csrf_route' => Route::has('sanctum.csrf-cookie'),
        'login_route' => Route::has('login'),
        'user_route' => Route::has('user'),
        'timestamp' => now(),
        'session_driver' => config('session.driver'),
        'cors_origins' => config('cors.allowed_origins')
    ]);
});

// No CSRF needed for API routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::get('/movies', [PublicMovieController::class, 'index']);
Route::get('/movies/coming-soon', [PublicMovieController::class, 'comingSoon']);
Route::get('/movies/live-now', [PublicMovieController::class, 'liveNow']);
Route::get('/movies/{movie}', [PublicMovieController::class, 'show']);

// Public food routes
Route::get('foods', [AdminFoodController::class, 'index']);
Route::get('foods/{food}', [AdminFoodController::class, 'show']);

Route::get('/showtimes/{id}/seats', function ($id) {
    $seats = \App\Models\Seat::where('showtime_id', $id)->get();
    return response()->json($seats);
});

// Protected routes with Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);
    
    // User routes
    Route::middleware('role:user')->group(function () {
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/user/bookings', [BookingController::class, 'userBookings']);
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
    });
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function (Request $request) {
            return response()->json([
                'message' => 'Admin dashboard data',
                'user' => $request->user(),
                'stats' => [
                    'total_users' => \App\Models\User::count(),
                    'total_movies' => \App\Models\Movie::count(),
                    'total_bookings' => \App\Models\Booking::count()
                ]
            ]);
        });
        
        // Movie management
        Route::apiResource('admin/movies', AdminMovieController::class);
        
        // Food management
        Route::apiResource('admin/foods', AdminFoodController::class);
        
        // User management
        Route::apiResource('admin/users', AdminUserController::class);
    });
    
    // Owner routes
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/dashboard', [\App\Http\Controllers\Owner\OwnerController::class, 'dashboard']);
        Route::get('/owner/reports/income', [\App\Http\Controllers\Owner\OwnerController::class, 'incomeReport']);
        Route::get('/owner/reports/expenses', [\App\Http\Controllers\Owner\OwnerController::class, 'expenseReport']);
    });
    
    // Kasir routes
    Route::middleware('role:kasir')->group(function () {
        Route::get('/kasir/dashboard', [\App\Http\Controllers\Kasir\KasirController::class, 'dashboard']);
        Route::post('/kasir/bookings', [\App\Http\Controllers\Kasir\KasirController::class, 'createBooking']);
        Route::put('/kasir/bookings/{id}/process', [\App\Http\Controllers\Kasir\KasirController::class, 'processBooking']);
        Route::post('/kasir/food-orders', [\App\Http\Controllers\Kasir\KasirController::class, 'createFoodOrder']);
    });
});