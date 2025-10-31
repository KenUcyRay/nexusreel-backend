<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,id',
            'payment_method' => 'required|in:qris,cash'
        ]);

        DB::beginTransaction();
        try {
            $showtime = Showtime::with('movie')->findOrFail($request->showtime_id);
            $seats = Seat::whereIn('id', $request->seat_ids)->get();
            
            // Check if seats are available
            foreach ($seats as $seat) {
                if ($seat->is_booked) {
                    throw new Exception("Seat {$seat->seat_number} is already booked");
                }
            }

            $totalAmount = $showtime->movie->price * count($seats);
            
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'showtime_id' => $request->showtime_id,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'invoice_number' => 'INV-' . time() . '-' . auth()->id(),
                'booking_status' => 'confirmed'
            ]);

            // Mark seats as booked
            foreach ($seats as $seat) {
                $seat->update(['is_booked' => true]);
                $booking->seats()->attach($seat->id);
            }

            DB::commit();
            return response()->json($booking->load('seats', 'showtime.movie'));
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show($id)
    {
        $booking = Booking::with('showtime.movie', 'seats', 'user')
            ->where('user_id', '=', Auth::id())
            ->where('id', '=', $id)
            ->firstOrFail();
        
        return response()->json($booking);
    }

    public function userBookings()
    {
        $bookings = Booking::with('showtime.movie', 'seats')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($bookings);
    }
}