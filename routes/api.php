<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PublicMovieController;
use App\Http\Controllers\PublicFoodController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\FoodController as AdminFoodController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\StudioController as AdminStudioController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MidtransController;
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

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'profile']);

// Public routes
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{id}', [MovieController::class, 'show']);

// Public food routes
Route::get('foods', [AdminFoodController::class, 'index']);
Route::get('foods/{food}', [AdminFoodController::class, 'show']);

// Public studio routes
Route::get('/studios', [StudioController::class, 'index']);
Route::get('/studios/{id}', [StudioController::class, 'show']);

// Public schedule routes
Route::get('/schedules', [ScheduleController::class, 'index']);
Route::get('/schedules/movie/{movieId}', [AdminScheduleController::class, 'getByMovie']);
Route::get('/schedules/{id}', [ScheduleController::class, 'show']);

// Payment routes
Route::post('/payment', [MidtransController::class, 'createTransaction']);
Route::post('/createTransaction', [MidtransController::class, 'createTransaction']);
Route::post('/kasir/payment', [MidtransController::class, 'createKasirTransaction']);
Route::post('/payment/callback', [MidtransController::class, 'callback']);
Route::post('/food-payment', [\App\Http\Controllers\FoodPaymentController::class, 'createPayment']);
Route::get('/midtrans/test', [\App\Http\Controllers\MidtransTestController::class, 'testConnection']);
Route::post('/midtrans/simulate-webhook', [\App\Http\Controllers\MidtransSimulatorController::class, 'simulateWebhook']);
Route::get('/midtrans/status/{orderId}', [\App\Http\Controllers\MidtransSimulatorController::class, 'checkTransactionStatus']);

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
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'stats']);
        
        // Movie management
        Route::apiResource('movies', AdminMovieController::class);
        
        // Food management
        Route::apiResource('foods', AdminFoodController::class);
        Route::patch('foods/{food}/toggle-availability', [AdminFoodController::class, 'toggleAvailability']);
        
        // User management
        Route::apiResource('users', AdminUserController::class);
        
        // Studio management
        Route::apiResource('studios', AdminStudioController::class);
        
        // Schedule management
        Route::apiResource('schedules', AdminScheduleController::class);
        Route::get('schedules-data', [AdminScheduleController::class, 'getMoviesAndStudios']);
        
        // Transaction management
        Route::get('transactions/food', [TransactionController::class, 'foodTransactions']);
        Route::get('dashboard/food-stats', [TransactionController::class, 'foodStats']);
        Route::get('transactions/food/{id}', [TransactionController::class, 'showFoodTransaction']);
        Route::get('transactions/movie', [TransactionController::class, 'movieTransactions']);
        Route::get('transactions/movie/{id}', [TransactionController::class, 'showMovieTransaction']);
    });
    
    // Studio management (public create/update/delete for admin)
    Route::middleware('role:admin')->group(function () {
        Route::post('/studios', [StudioController::class, 'store']);
        Route::put('/studios/{id}', [StudioController::class, 'update']);
        Route::delete('/studios/{id}', [StudioController::class, 'destroy']);
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