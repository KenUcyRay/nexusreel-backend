<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FoodOrder;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function dashboard()
    {
        $todayBookings = Booking::whereDate('created_at', today())->count();
        $todayRevenue = Booking::whereDate('created_at', today())
            ->where('booking_status', 'confirmed')
            ->sum('total_amount');
        
        return response()->json([
            'today_bookings' => $todayBookings,
            'today_revenue' => $todayRevenue,
        ]);
    }

    public function createBooking(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|array',
            'payment_method' => 'required|in:qris,cash',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Create offline booking logic here
            $booking = Booking::create([
                'user_id' => null, // Offline booking
                'showtime_id' => $request->showtime_id,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'invoice_number' => 'OFF-' . time(),
                'booking_status' => 'confirmed'
            ]);

            DB::commit();
            return response()->json($booking);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function processBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['booking_status' => 'confirmed']);
        
        return response()->json(['message' => 'Booking processed successfully']);
    }

    public function createFoodOrder(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'food_id' => 'required|exists:foods,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $food = Food::findOrFail($request->food_id);
        $subtotal = $food->price * $request->quantity;

        $order = FoodOrder::create([
            'booking_id' => $request->booking_id,
            'food_id' => $request->food_id,
            'quantity' => $request->quantity,
            'subtotal' => $subtotal,
        ]);

        return response()->json($order);
    }
}