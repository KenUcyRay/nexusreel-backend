<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set'])
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
});

// Midtrans webhook route (must be accessible without CSRF)
Route::post('/midtrans/notification', [\App\Http\Controllers\MidtransController::class, 'callback']);
